#!/bin/bash
set -e

# Detectar el usuario del servidor web (www-data en Apache, laravel en FPM/Alpine)
WWW_USER="www-data"
if id "laravel" >/dev/null 2>&1; then
    WWW_USER="laravel"
fi

if [ "$(id -u)" = '0' ]; then
    chown -R $WWW_USER:$WWW_USER /var/www/storage /var/www/bootstrap/cache
fi

# Verificar si las dependencias de Composer no están instaladas
if [ ! -d "/var/www/vendor" ]; then
    echo "Instalando dependencias de Composer..."
    composer install --no-interaction --no-progress --no-dev
fi

# Verificar si no existe un archivo .env
if [ ! -f "/var/www/.env" ]; then
    echo "Creando .env y generando App Key..."
    cp .env.example .env
    php artisan key:generate --force
fi

# Ejecutar migraciones si se solicita
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Ejecutando migraciones..."
    php artisan migrate --force
fi

# Ejecutar optimizaciones en producción
if [ "$APP_ENV" = "production" ]; then
    echo "Optimizando Laravel para producción..."
    php artisan optimize
fi

# Ejecutar el comando pasado al contenedor (e.g. php-fpm)
echo "Iniciando servicio..."
exec "$@"
