#!/bin/bash

# Verificar si las dependencias de Composer no están instaladas
if [ ! -d "/var/www/vendor" ]; then
    echo "Instalando dependencias de Composer..."
    composer install --no-interaction --no-progress
fi

# Verificar si no existe un archivo .env
if [ ! -f "/var/www/.env" ]; then
    echo "Creando .env y generando App Key..."
    cp .env.example .env
    php artisan key:generate
fi

# Asignar permisos adecuados para Laravel (importante si se ejecuta root)
# Intentamos cambiar los permisos pero no fallamos si hay problemas con los volúmenes en Windows
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache || true
chmod -R 775 /var/www/storage /var/www/bootstrap/cache || true

# Ejecutar el comando pasado al contenedor (e.g. php-fpm)
echo "Iniciando servicio..."
exec "$@"
