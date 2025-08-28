# syntax=docker/dockerfile:1

########## Stage 1: vendor (Composer) ##########
FROM composer:2 AS vendor
WORKDIR /app
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copiar composer.json y lock desde subcarpeta app/
COPY app/composer.json app/composer.lock ./

# Instalar dependencias (ignora ext-intl en build, en runtime sí estará)
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader \
    --ignore-platform-req=ext-intl

########## Stage 2: PHP + Apache ##########
FROM php:8.2-apache

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    unzip git curl libzip-dev libicu-dev \
 && docker-php-ext-install zip mysqli pdo_mysql intl \
 && rm -rf /var/lib/apt/lists/*

# Apache -> public y mod_rewrite
RUN a2enmod rewrite \
 && sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
 && printf "\n<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>\n" >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Copiar vendor generado en el stage vendor
COPY --from=vendor /app/vendor /var/www/html/vendor

# Copiar el resto del proyecto desde /app
COPY app/. .

# Crear carpeta writable y dar permisos correctos
RUN mkdir -p /var/www/html/writable/{cache,logs,session,uploads} \
 && chown -R www-data:www-data /var/www/html/writable \
 && find /var/www/html/writable -type d -exec chmod 775 {} \; \
 && find /var/www/html/writable -type f -exec chmod 664 {} \;

 RUN mkdir -p /var/www/html/writable/cache \
    && chmod -R 755 /var/www/html/writable \
    && chown -R www-data:www-data /var/www/html/writable

ENV CI_ENVIRONMENT=production
EXPOSE 80
