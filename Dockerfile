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

# Configurar directorio de trabajo
WORKDIR /var/www

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

# Stage 4: Production Image
FROM php-base AS production

# Usar configuración de producción de PHP
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Configurar OPcache
COPY docker/php/opcache.ini "$PHP_INI_DIR/conf.d/opcache.ini"

# Crear usuario no privilegiado para la aplicación
RUN addgroup -S laravel && adduser -S laravel -G laravel

# Copiar Composer binario
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar dependencias de Composer a un temporal para dump-autoload posterior
COPY --from=composer-build /app/vendor /var/www/vendor

# Copiar assets compilados
COPY --from=node-build /app/public/build /var/www/public/build

# Copiar el resto de la aplicación con los permisos correctos
COPY --chown=laravel:laravel . /var/www

# Generar el autoloader de Composer optimizado (sin scripts para evitar fallos de Artisan en build)
RUN composer dump-autoload --optimize --no-dev --no-scripts

# Ajustar permisos finales para Laravel
RUN chown -R laravel:laravel /var/www/storage /var/www/bootstrap/cache

# Cambiar al usuario no raíz
USER laravel

# Exponer el puerto
EXPOSE 9000

# Script de entrada
COPY --chown=laravel:laravel docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
