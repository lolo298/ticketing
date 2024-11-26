FROM php:8.2-apache


RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite

# RUN echo "nameserver 192.168.1.115" > /etc/resolv.conf

COPY . /var/www/html

EXPOSE 80