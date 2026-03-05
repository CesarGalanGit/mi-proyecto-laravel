# Stage 1: PHP Base
FROM php:8.3-fpm-alpine AS php-base

# Instalar dependencias del sistema y extensiones de PHP
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    icu-dev \
    oniguruma-dev \
    bash \
    mysql-client \
    libxml2-dev

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip opcache xml dom

# Instalar extensión de Redis
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Crear usuario no privilegiado para la aplicación
RUN addgroup -S laravel && adduser -S laravel -G laravel

# Configurar directorio de trabajo
WORKDIR /var/www
RUN chown laravel:laravel /var/www

# Stage 2: Composer Build
FROM composer:latest AS composer-build
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Stage 3: Node Build
FROM node:20-alpine AS node-build
WORKDIR /app
COPY package.json package-lock.json vite.config.js ./
RUN npm install
COPY resources ./resources
RUN npm run build

# Stage 4: Production Image (Apache for single-container deployment)
FROM php:8.3-apache-alpine AS production

# Instalar extensiones necesarias en la imagen de producción final
RUN apk add --no-cache \
    icu-dev \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev \
    bash \
    mysql-client

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip opcache xml dom

# Configurar Apache: Cambiar DocumentRoot a /var/www/public y habilitar mod_rewrite
ENV APACHE_DOCUMENT_ROOT /var/www/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/httpd.conf \
    && sed -ri -e 's!/var/www/localhost/htdocs!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/httpd.conf \
    && echo "LoadModule rewrite_module modules/mod_rewrite.so" >> /etc/apache2/httpd.conf \
    && echo "<Directory ${APACHE_DOCUMENT_ROOT}>" >> /etc/apache2/httpd.conf \
    && echo "    AllowOverride All" >> /etc/apache2/httpd.conf \
    && echo "    Require all granted" >> /etc/apache2/httpd.conf \
    && echo "</Directory>" >> /etc/apache2/httpd.conf

# Usar configuración de producción de PHP
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Configurar OPcache
COPY docker/php/opcache.ini "$PHP_INI_DIR/conf.d/opcache.ini"

# Copiar Composer binario
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar dependencias de Composer
COPY --from=composer-build /app/vendor /var/www/vendor

# Copiar assets compilados
COPY --from=node-build /app/public/build /var/www/public/build

# Copiar el resto de la aplicación con los permisos correctos
WORKDIR /var/www
COPY --chown=www-data:www-data . /var/www

# Crear directorios necesarios para Laravel
RUN mkdir -p /var/www/storage/app/public \
    /var/www/storage/framework/cache \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /var/www/storage/logs \
    /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Generar el autoloader de Composer optimizado
RUN composer dump-autoload --optimize --no-dev --no-scripts

# Exponer el puerto de Apache
EXPOSE 80

# Script de entrada
COPY --chown=www-data:www-data docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Render espera que el contenedor corra en el puerto que ellos asignan, 
# pero Apache por defecto va al 80.
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["httpd", "-D", "FOREGROUND"]
