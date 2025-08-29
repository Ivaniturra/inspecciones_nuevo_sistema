# syntax=docker/dockerfile:1

########## Stage 1: vendor (Composer) ##########
FROM composer:2 AS vendor
WORKDIR /app
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY app/composer.json app/composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader \
    --ignore-platform-req=ext-intl

########## Stage 2: PHP + Apache ##########
FROM php:8.2-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
      unzip git curl libzip-dev libicu-dev \
 && docker-php-ext-install zip mysqli pdo_mysql intl \
 && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite \
 && sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
 && { \
      echo '<Directory /var/www/html/public>'; \
      echo '    AllowOverride All'; \
      echo '    Require all granted'; \
      echo '</Directory>'; \
    } >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

COPY --from=vendor /app/vendor /var/www/html/vendor
COPY app/. .

# Copiamos el entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENV CI_ENVIRONMENT=production
EXPOSE 80

# Copiar entrypoint desde la carpeta correcta
COPY scripts/entrypoint.sh /entrypoint.sh
# Normaliza CRLF (si editas en Windows) y marca ejecutable
RUN sed -i 's/\r$//' /entrypoint.sh && chmod +x /entrypoint.sh

CMD ["/entrypoint.sh"]