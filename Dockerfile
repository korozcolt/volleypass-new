# Etapa 1: Composer build
FROM composer:2 AS build

WORKDIR /app
COPY . .
RUN composer install --optimize-autoloader --no-dev

# Etapa 2: PHP 8.3 con FPM
FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    libzip-dev \
    libicu-dev \
    libexif-dev \
    && docker-php-ext-install \
      pdo_mysql mbstring zip exif pcntl bcmath gd intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

COPY --from=build /app /var/www

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
