FROM php:8.4-cli
RUN apt-get update && apt-get install -y unzip libcurl4-openssl-dev pkg-config libssl-dev git
RUN pecl install mongodb && docker-php-ext-enable mongodb
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
COPY . .
RUN composer install --no-dev --optimize-autoloader
CMD php artisan serve --host=0.0.0.0 --port=8000
