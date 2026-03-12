# Stage 1: Base image with all necessary PHP extensions
FROM php:8.3-apache AS base

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libcurl4-openssl-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    mariadb-client \
    && rm -rf /var/lib/apt/lists/*

# Install and enable PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip opcache xml dom curl

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Configure Apache: Change DocumentRoot to /var/www/public and enable mod_rewrite
ENV APACHE_DOCUMENT_ROOT /var/www/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && printf "<Directory ${APACHE_DOCUMENT_ROOT}>\n\tAllowOverride All\n\tRequire all granted\n</Directory>\n" >> /etc/apache2/apache2.conf \
    && a2enmod rewrite

# Stage 2: Composer dependencies build
FROM composer:latest AS composer-build
WORKDIR /app
COPY composer.json composer.lock ./
# Note: No-dev and optimized autoloader are handled here for efficiency
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Stage 3: Node assets build
FROM node:20-alpine AS node-build
WORKDIR /app
COPY package.json package-lock.json vite.config.js ./
RUN npm install
COPY resources ./resources
RUN npm run build

# Stage 4: Production Image
FROM base AS production

# Use production PHP configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Copy custom configurations (OPcache, etc.)
COPY docker/php/opcache.ini "$PHP_INI_DIR/conf.d/opcache.ini"

# Ensure Composer is available if needed (entrypoint might use it)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Vendor and Assets from previous stages
WORKDIR /var/www
COPY --from=composer-build /app/vendor /var/www/vendor
COPY --from=node-build /app/public/build /var/www/public/build

# Copy the rest of the application files
COPY --chown=www-data:www-data . /var/www

# Create necessary Laravel directories and set permissions
RUN mkdir -p /var/www/storage/app/public \
    /var/www/storage/framework/cache \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /var/www/storage/logs \
    /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Generate optimized Composer autoloader
RUN composer dump-autoload --optimize --no-dev --no-scripts

# Expose Apache port
EXPOSE 80

# Configure Entrypoint
COPY --chown=www-data:www-data docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
