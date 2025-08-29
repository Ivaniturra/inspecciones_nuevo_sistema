# syntax=docker/dockerfile:1

########## Stage 1: vendor (Composer) ##########
FROM composer:2 AS vendor
WORKDIR /app
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copia composer.json/lock desde tu subcarpeta app/
COPY app/composer.json app/composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader \
    --ignore-platform-req=ext-intl

########## Stage 2: PHP + Apache ##########
FROM php:8.2-apache

# Paquetes y extensiones PHP necesarias
RUN apt-get update && apt-get install -y --no-install-recommends \
      unzip git curl libzip-dev libicu-dev \
 && docker-php-ext-install zip mysqli pdo_mysql intl \
 && rm -rf /var/lib/apt/lists/*

# Apache -> servir /public y activar rewrite
RUN a2enmod rewrite \
 && sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
 && { \
      echo '<Directory /var/www/html/public>'; \
      echo '    AllowOverride All'; \
      echo '    Require all granted'; \
      echo '</Directory>'; \
    } >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Copia vendor del stage de Composer
COPY --from=vendor /app/vendor /var/www/html/vendor

# Copia el proyecto (tu código está en la subcarpeta app/)
COPY app/. .

# Crear y fijar permisos de rutas escribibles (idempotente)
# - setgid 2775 en directorios para heredar grupo
# - archivos 664, dirs 2775
RUN mkdir -p /var/www/html/writable/{cache,logs,session,uploads} /var/www/html/public/uploads \
 && chown -R www-data:www-data /var/www/html/writable /var/www/html/public/uploads \
 && find /var/www/html/writable            -type d -exec chmod 2775 {} \; \
 && find /var/www/html/public/uploads      -type d -exec chmod 2775 {} \; \
 && find /var/www/html/writable            -type f -exec chmod 0664 {} \; \
 && find /var/www/html/public/uploads      -type f -exec chmod 0664 {} \;

# Entorno y puerto
ENV CI_ENVIRONMENT=production
EXPOSE 80

# Proceso principal de Apache
CMD ["apache2-foreground"]
