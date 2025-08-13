FROM php:8.2-apache

# Paquetes del sistema y extensiones PHP
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    libicu-dev \ 
 && docker-php-ext-install zip mysqli pdo pdo_mysql intl \
 && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite y apuntar a /public
RUN a2enmod rewrite \
 && sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
 && printf "\n<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>\n" >> /etc/apache2/apache2.conf \
 && printf "\nServerName localhost\n" >> /etc/apache2/conf-available/servername.conf \
 && a2enconf servername

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY ./app /var/www/html