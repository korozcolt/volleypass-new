# Etapa 1: Composer build
FROM composer:2 AS build

# Instalar PHP y extensiones necesarias en Alpine
RUN apk add --no-cache \
    php83 \
    php83-intl \
    php83-gd \
    php83-exif \
    php83-pdo \
    php83-pdo_mysql \
    php83-mbstring \
    php83-xml \
    php83-tokenizer \
    php83-zip \
    php83-openssl \
    php83-session \
    php83-fileinfo \
    && ln -s /usr/bin/php83 /usr/bin/php

# Crear archivo php.ini básico para que Composer detecte las extensiones
RUN echo "extension=intl.so" > /etc/php83/conf.d/intl.ini && \
    echo "extension=gd.so" > /etc/php83/conf.d/gd.ini && \
    echo "extension=exif.so" > /etc/php83/conf.d/exif.ini

WORKDIR /app
COPY . .

# Instalar dependencias ignorando temporalmente los requisitos de plataforma
RUN composer install --optimize-autoloader --no-dev --ignore-platform-req=ext-intl --ignore-platform-req=ext-gd --ignore-platform-req=ext-exif

# Etapa 2: PHP 8.3 con FPM
FROM php:8.3-fpm

# Instalar dependencias y extensiones necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    libzip-dev \
    libicu-dev \
    libexif-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
      pdo_mysql mbstring zip exif pcntl bcmath gd intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

# Copiar la aplicación desde la etapa de construcción
COPY --from=build /app /var/www

# Configurar permisos
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]