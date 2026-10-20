FROM php:8.3-apache

RUN docker-php-ext-install pdo pdo_mysql mysqli \
    && a2enmod rewrite

# DocumentRoot apuntando a /public y .htaccess habilitado
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf
