FROM php:8.2-apache


RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer


COPY . /var/www/html

RUN composer install



EXPOSE 80