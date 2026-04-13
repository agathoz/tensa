FROM php:8.2-apache

# Habilitar mod_rewrite para urls amigables si se necesitan
RUN a2enmod rewrite

# Instalar extensiones necesarias (mysqli, pdo_mysql, gd, zip) para perfiles y PDFs simulados
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli zip

# Instalar Composer por si se necesita PHPMailer / mPDF para facturas de prueba
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
