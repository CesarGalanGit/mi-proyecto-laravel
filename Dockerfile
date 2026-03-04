# Stage 1: Build assets with Node.js
FROM node:20 AS node-builder
WORKDIR /app
COPY package*.json ./
# Clean cache and install for clean slate
RUN npm cache clean --force && npm install
COPY . .
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

# Copy application files
COPY . .
COPY --from=node-builder /app/public/build ./public/build

# Install Composer dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Optimize Laravel
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Production environment variables
ENV RUN_MIGRATIONS=false
ENV FRANKENPHP_CONFIG="import /etc/caddy/Caddyfile.d/*.caddy"

# The base image already exposes 80 and 443
EXPOSE 80
EXPOSE 443

# Start FrankenPHP
CMD ["frankenphp", "php-server"]
