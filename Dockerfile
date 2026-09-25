FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite headers

COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini
COPY src/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
