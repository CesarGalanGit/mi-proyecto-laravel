#!/bin/bash
set -e

# Si el usuario es root, ajustar permisos y cambiar al usuario laravel si es necesario
# (Aunque en el Dockerfile ya definimos USER laravel, por si acaso se corre distinto)
if [ "$(id -u)" = '0' ]; then
    chown -R laravel:laravel /var/www/storage /var/www/bootstrap/cache
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

# Ejecutar optimizaciones en producción
if [ "$APP_ENV" = "production" ]; then
    echo "Optimizando Laravel para producción..."
    php artisan optimize
fi

# Ejecutar el comando pasado al contenedor (e.g. php-fpm)
echo "Iniciando servicio..."
exec "$@"
