# syntax=docker/dockerfile:1

########## Stage 1: vendor con PHP CLI + intl ##########
FROM php:8.2-cli AS vendor
WORKDIR /app

RUN apt-get update && apt-get install -y git unzip libzip-dev libicu-dev \
 && docker-php-ext-install zip intl

# Instalar Composer
RUN php -r "copy('https://getcomposer.org/installer','composer-setup.php');" \
 && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && rm composer-setup.php
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY app/composer.json app/composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

########## Stage 2: PHP + Apache ##########
FROM php:8.2-apache

RUN apt-get update && apt-get install -y unzip git curl libzip-dev libicu-dev \
 && docker-php-ext-install zip mysqli pdo_mysql intl \
 && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite \
 && sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
 && printf "\n<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>\n" >> /etc/apache2/apache2.conf

WORKDIR /var/www/html
COPY --from=vendor /app/vendor /var/www/html/vendor
COPY app/. .

RUN chown -R www-data:www-data /var/www/html/writable \
 && find /var/www/html/writable -type d -exec chmod 775 {} \; \
 && find /var/www/html/writable -type f -exec chmod 664 {} \;

ENV CI_ENVIRONMENT=production
EXPOSE 80
