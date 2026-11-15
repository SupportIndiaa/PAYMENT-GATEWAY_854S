FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql mysqli \
    && a2enmod rewrite \
    && sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf \
    && sed -i 's/:80>/:10000>/' /etc/apache2/sites-available/000-default.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --optimize-autoloader

EXPOSE 10000
CMD ["apache2-foreground"]
