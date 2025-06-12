FROM php:8.3-apache

# pdo_mysql package
RUN docker-php-ext-install pdo_mysql