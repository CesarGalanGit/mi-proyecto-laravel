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

# Ejecutar seeders si se solicita
if [ "$RUN_SEEDER" = "true" ]; then
    echo "Ejecutando seeders..."
    php artisan db:seed --force
fi

# Ejecutar optimizaciones en producción
if [ "$APP_ENV" = "production" ]; then
    echo "Optimizando Laravel para producción..."
    php artisan optimize
fi

# Auto-import Scout index on first boot (if Algolia driver, not queued, and not yet imported)
if [ "${SCOUT_DRIVER:-}" = "algolia" ] && [ "${SCOUT_QUEUE:-}" != "true" ] && [ ! -f "/var/www/storage/framework/scout-imported" ]; then
    echo "Importando datos a Algolia (Scout)..."
    set +e
    php artisan scout:import "App\Models\Car"
    IMP_EXIT=$?
    set -e
    if [ $IMP_EXIT -eq 0 ]; then
        touch /var/www/storage/framework/scout-imported
        echo "Importación completada."
        # Sincronizar configuración de índices (searchableAttributes, faceting, etc.)
        echo "Sincronizando configuración de índices Scout/Algolia..."
        set +e
        php artisan scout:sync-index-settings
        SYNC_EXIT=$?
        set -e
        if [ $SYNC_EXIT -eq 0 ]; then
            echo "Configuración de índices sincronizada."
        else
            echo "Advertencia: falló la sincronización de índices (código $SYNC_EXIT)."
        fi
    else
        echo "Advertencia: la importación a Algolia falló (código $IMP_EXIT). Continuando..."
    fi
fi

# Ejecutar el comando pasado al contenedor (e.g. php-fpm)
echo "Iniciando servicio..."
exec "$@"
