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

RUN cp .env.example .env && \
    php artisan key:generate --force

RUN composer install --no-dev --optimize-autoloader

RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache --force

EXPOSE 80 443

USER www-data

ENTRYPOINT ["php", "artisan", "octane:frankenphp"]
