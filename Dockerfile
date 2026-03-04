FROM dunglas/frankenphp:alpine AS builder

RUN install-php-extensions \
    pcntl \
    pdo_mysql \
    pdo_pgsql \
    pdo_sqlite \
    mbstring \
    xml \
    curl \
    zip \
    gd

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

COPY . .

RUN php artisan key:generate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache --force

FROM dunglas/frankenphp:alpine

RUN install-php-extensions \
    pcntl \
    pdo_mysql \
    pdo_pgsql \
    pdo_sqlite \
    mbstring \
    xml \
    curl \
    zip \
    gd

WORKDIR /app

COPY --from=builder /app/vendor ./vendor
COPY --from=builder /app/public ./public
COPY --from=builder /app/resources ./resources
COPY --from=builder /app/storage ./storage
COPY --from=builder /app/bootstrap ./bootstrap
COPY --from=builder /app/config ./config
COPY --from=builder /app/routes ./routes
COPY --from=builder /app/app ./app
COPY --from=builder /app/.env ./.env
COPY --from=builder /app/artisan ./
COPY --from=builder /app/bootstrap/app ./bootstrap/app
COPY --from=builder /app/database ./database

EXPOSE 80 443

USER www-data

ENTRYPOINT ["php", "artisan", "octane:frankenphp"]
