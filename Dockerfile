# Etapa 1: Build
FROM composer:2 AS build

WORKDIR /app
COPY . .
RUN composer install --optimize-autoloader --no-dev

# Etapa 2: PHP 8.4 de shivammathur
FROM ghcr.io/shivammathur/php:8.4-fpm

# Instala extensiones necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

WORKDIR /var/www

# Copia app desde el contenedor anterior
COPY --from=build /app /var/www

# Permisos para Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
