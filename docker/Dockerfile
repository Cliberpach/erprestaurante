FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpng-dev libonig-dev libxml2-dev

RUN docker-php-ext-install pdo pdo_mysql

RUN pecl install redis \
    && docker-php-ext-enable redis

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf

# ← AGREGA ESTO: AllowOverride All para que .htaccess funcione
RUN sed -ri -e 's!AllowOverride None!AllowOverride All!g' \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html
