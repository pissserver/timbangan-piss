FROM php:5.6-apache

RUN docker-php-ext-install mysql mysqli pdo pdo_mysql \
    && a2enmod rewrite

COPY . /var/www/html/timbangan-piss

RUN sed -i 's|^RewriteBase /timbangan-piss/|RewriteBase /|' /var/www/html/timbangan-piss/.htaccess \
    && sed -i 's|/var/www/html|/var/www/html/timbangan-piss|' /etc/apache2/sites-available/000-default.conf

EXPOSE 80
