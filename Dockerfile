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

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN cp .env.example .env

RUN php artisan key:generate --force || echo "Key generation skipped"

RUN php artisan config:cache --no-interaction || echo "Config cache skipped"

RUN php artisan route:cache --no-interaction || echo "Route cache skipped"

RUN php artisan view:cache --no-interaction || echo "View cache skipped"

EXPOSE 80 443

USER www-data

ENTRYPOINT ["php", "artisan", "octane:frankenphp"]
