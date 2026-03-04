# Stage 1: Build assets with Node.js
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci --silent
COPY vite.config.js ./
COPY resources ./resources
RUN npm run build

# Stage 2: PHP base with extensions
FROM dunglas/frankenphp:1.3-php8.3-alpine AS php-base

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    git \
    curl \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    zlib-dev \
    libzip-dev \
    && install-php-extensions \
    bcmath \
    pcntl \
    pdo_mysql \
    gd \
    intl \
    zip \
    opcache \
    fileinfo \
    mbstring \
    tokenizer \
    xml \
    dom \
    redis

# Configure PHP for production
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=10'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.save_comments=1'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'realpath_cache=4096'; \
    echo 'realpath_cache_ttl=600'; \
} > /usr/local/etc/php/conf.d/opcache-recommended.ini

WORKDIR /app

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Stage 3: Development environment (target: dev)
FROM php-base AS dev

# Install Node.js for development
RUN apk add --no-cache nodejs npm

# Copy built assets from node stage
COPY --from=node-builder /app/public/build ./public/build

# Copy all node_modules from node-builder (same architecture)
COPY --from=node-builder /app/node_modules ./node_modules

# Copy application files
COPY . .

# Install all dependencies (including dev) - only if node_modules not present
RUN if [ ! -d "node_modules" ]; then \
        npm ci --silent; \
    fi && \
    composer install --prefer-dist --no-interaction

# Set permissions for development (www-data uid 82 in FrankenPHP)
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Copy entrypoint for development
COPY <<'EOF' /usr/local/bin/start-dev.sh
#!/bin/sh
set -e

# Create .env if it doesn't exist
if [ ! -f .env ]; then
    cp .env.example .env
    echo "Created .env from .env.example"
fi

# Ensure APP_KEY is set
if ! grep -q "^APP_KEY=" .env; then
    php artisan key:generate --force
    echo "Generated new APP_KEY"
fi

# Run migrations if enabled
RUN_MIGRATIONS=${RUN_MIGRATIONS:-true}
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force --no-interaction
fi

# Start Vite dev server in background if needed
if [ -f package.json ] && grep -q '"dev"' package.json; then
    echo "Starting Vite dev server on port 5173..."
    npm run dev &
    VITE_PID=$!
    trap "kill $VITE_PID" EXIT INT TERM
fi

echo "Starting FrankenPHP in development mode..."
exec frankenphp php-server
EOF

RUN chmod +x /usr/local/bin/start-dev.sh

EXPOSE 80
EXPOSE 443

CMD ["/usr/local/bin/start-dev.sh"]

# Stage 4: Production environment (default target)
FROM php-base AS prod

# Copy built assets from node stage
COPY --from=node-builder /app/public/build ./public/build

# Copy application files
COPY . .

# Install production dependencies only
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts && \
    composer dump-autoload --optimize && \
    php artisan package:discover --ansi

# Set permissions (www-data uid 82)
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Copy entrypoint for production (with optimizations)
COPY <<'EOF' /usr/local/bin/start.sh
#!/bin/sh
set -e

# Wait for database to be ready (max 60 seconds)
echo "Waiting for database connection..."
timeout=60
while ! php -r "new PDO('mysql:host=${DB_HOST};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
    timeout=$((timeout - 2))
    if [ $timeout -le 0 ]; then
        echo "Database connection timeout"
        exit 1
    fi
    sleep 2
done

# Create .env if it doesn't exist
if [ ! -f .env ]; then
    cp .env.example .env
    echo "Created .env from .env.example"
fi

# Ensure APP_KEY is set
if ! grep -q "^APP_KEY=" .env; then
    php artisan key:generate --force
    echo "Generated new APP_KEY"
fi

# Run migrations if enabled (default: true)
RUN_MIGRATIONS=${RUN_MIGRATIONS:-true}
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force --no-interaction
fi

# Optimize for production (only if APP_ENV=production)
if [ "${APP_ENV}" = "production" ]; then
    echo "Optimizing Laravel for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "Starting FrankenPHP..."
exec frankenphp php-server
EOF

RUN chmod +x /usr/local/bin/start.sh

EXPOSE 80
EXPOSE 443

CMD ["/usr/local/bin/start.sh"]

# Stage 5: Queue Worker (for production)
FROM php-base AS worker

# Copy application files
COPY . .

# Install production dependencies
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts && \
    composer dump-autoload --optimize && \
    php artisan package:discover --ansi

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Copy entrypoint for worker
COPY <<'EOF' /usr/local/bin/start-worker.sh
#!/bin/sh
set -e

# Wait for database and redis
echo "Waiting for database and redis..."
timeout=60
while ! php -r "new PDO('mysql:host=${DB_HOST};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
    timeout=$((timeout - 2))
    if [ $timeout -le 0 ]; then
        echo "Database connection timeout"
        exit 1
    fi
    sleep 2
done

# Ensure APP_KEY is set (needed for queue workers)
if ! grep -q "^APP_KEY=" .env; then
    cp .env.example .env
    php artisan key:generate --force
fi

echo "Starting queue worker..."
exec php artisan queue:work --tries=3 --timeout=90
EOF

RUN chmod +x /usr/local/bin/start-worker.sh

CMD ["/usr/local/bin/start-worker.sh"]
