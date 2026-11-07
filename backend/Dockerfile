# Menggunakan versi image FrankenPHP yang eksplisit (bukan latest)
FROM dunglas/frankenphp:1.1.5-php8.3-alpine

# Install ekstensi sistem yang dibutuhkan Laravel & MySQL
RUN apk add --no-cache \
    icu-dev \
    libzip-dev \
    sqlite-dev \
    mysql-client \
    && docker-php-ext-install pdo pdo_sqlite pdo_mysql zip bcmath

# Salin Composer dari image resmi
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Salin file dependensi composer
COPY composer.json composer.lock ./

# Install dependensi composer
RUN composer install --no-autoloader --no-scripts --prefer-dist

# Salin seluruh kode aplikasi
COPY . .

# Generate autoloader teroptimasi
RUN composer dump-autoload --optimize

EXPOSE 8000

# Jalankan server FrankenPHP melayani folder public/ di port 8000
CMD ["frankenphp", "php-server", "--listen", ":8000", "--root", "public/"]