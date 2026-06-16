# Menggunakan image PHP 8.2 (sesuaikan jika kamu pakai PHP versi 8.1 atau 8.3)
FROM php:8.2-cli

# Install dependensi sistem yang dibutuhkan untuk MongoDB
RUN apt-get update && apt-get install -y \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    git \
    unzip

# Install ekstensi MongoDB untuk PHP
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Atur folder kerja
WORKDIR /app

# Copy semua file Laravel ke dalam container
COPY . .

# Install paket Laravel (tanpa paket dev untuk produksi)
RUN composer install --no-dev --optimize-autoloader

# Atur permission
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Jalankan server bawaan Laravel, bind ke port dari Render
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}