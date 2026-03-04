# Stage 1: Build assets with Node.js
FROM node:20 AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY vite.config.js ./
COPY resources ./resources
RUN npm run build

# Stage 2: Production PHP environment
FROM dunglas/frankenphp:1.3-php8.3-alpine

# Install PHP extensions
RUN install-php-extensions \
    pcntl \
    pdo_mysql \
    gd \
    intl \
    zip \
    opcache

WORKDIR /app

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependencies optimizing cache layer
COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy ALL application files
COPY . .

# Copy built assets from node stage
COPY --from=node-builder /app/public/build ./public/build

# Finish installation (scripts like package:discover need the full app)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Expose ports (FrankenPHP handles HTTP/HTTPS)
EXPOSE 80
EXPOSE 443

# Entrypoint script to run optimizations at runtime (when .env is available)
COPY <<'EOF' /usr/local/bin/start.sh
#!/bin/sh
set -e

# Create .env if it doesn't exist, so key:generate doesn't fail
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Generate key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Run migrations if enabled
if [ "$RUN_MIGRATIONS" = "true" ]; then
    php artisan migrate --force
fi

# Cache config/routes/views at runtime (when env vars are available)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start FrankenPHP
exec frankenphp php-server
EOF

RUN chmod +x /usr/local/bin/start.sh

CMD ["/usr/local/bin/start.sh"]
