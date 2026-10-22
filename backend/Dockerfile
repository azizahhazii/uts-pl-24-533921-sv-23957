# Menggunakan versi image yang eksplisit (bukan latest)
FROM php:8.3-fpm-alpine

# Install ekstensi sistem yang dibutuhkan Laravel & SQLite
RUN apk add --no-cache \
    icu-dev \
    libzip-dev \
    sqlite-dev \
    && docker-php-ext-install pdo pdo_sqlite zip bcmath

# Salin Composer dari image resmi
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 1. Salin composer.json dan composer.lock terlebih dahulu untuk memanfaatkan Layer Caching Docker
COPY composer.json composer.lock ./

# 2. Install dependensi tanpa autoloader/script dulu
RUN composer install --no-autoloader --no-scripts --no-dev --prefer-dist

# 3. Salin seluruh kode aplikasi
COPY . .

# 4. Generate autoloader teroptimasi
RUN composer dump-autoload --optimize

EXPOSE 9000
CMD ["php-fpm"]