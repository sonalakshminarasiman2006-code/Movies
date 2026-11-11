FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    default-mysql-client \
    && docker-php-ext-install mysqli pdo pdo_mysql

RUN a2enmod rewrite

RUN sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf

RUN sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/' /etc/apache2/sites-available/000-default.conf

COPY . /var/www/html/

RUN printf '<Directory /var/www/html>\n\
Options FollowSymLinks\n\
AllowOverride All\n\
Require all granted\n\
DirectoryIndex index.php index.html\n\
</Directory>\n' > /etc/apache2/conf-available/moviebooking.conf

RUN a2enconf moviebooking

RUN chown -R www-data:www-data /var/www/html

EXPOSE 10000
