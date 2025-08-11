FROM php:8.3-apache

WORKDIR /var/www/html

RUN chown -R www-data:www-data /var/www/html