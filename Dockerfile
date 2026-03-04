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
    gd \
    redis

WORKDIR /app

COPY . /app

RUN cp .env.example .env && \
    php artisan key:generate --force && \
    composer install --no-dev --optimize-autoloader && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

EXPOSE 80 443

ENTRYPOINT ["php", "artisan", "octane:frankenphp"]
