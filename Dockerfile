# syntax=docker/dockerfile:1
FROM php:8.2-apache

# Paquetes del sistema y extensiones PHP
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libzip-dev \
    libicu-dev \
 && docker-php-ext-install zip mysqli pdo_mysql intl \
 && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite y apuntar DocumentRoot a /public
RUN a2enmod rewrite \
 && sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
 && printf "\n<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>\n" >> /etc/apache2/apache2.conf \
 && printf "\nServerName localhost\n" >> /etc/apache2/conf-available/servername.conf \
 && a2enconf servername

# Composer (desde imagen oficial)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Trabajo en /var/www/html
WORKDIR /var/www/html

# 1) Copiamos archivos de Composer primero para aprovechar cache
COPY composer.json composer.lock ./

# 2) Instalamos dependencias PHP (genera vendor/)
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# 3) Copiamos el resto del proyecto
COPY . .

# Permisos para writable/
RUN chown -R www-data:www-data /var/www/html/writable \
 && find /var/www/html/writable -type d -exec chmod 775 {} \; \
 && find /var/www/html/writable -type f -exec chmod 664 {} \;

# Entorno (opcional)
ENV CI_ENVIRONMENT=production

EXPOSE 80
