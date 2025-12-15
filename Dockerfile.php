FROM php:8.2-apache

# Copy entire project into Apache root
COPY . /var/www/html/

# Enable Apache rewrite
RUN a2enmod rewrite

# Set router.php as the default index file
RUN echo "DirectoryIndex router.php index.php index.html" >> /etc/apache2/apache2.conf

# Allow access and disable directory listing
RUN printf "<Directory /var/www/html>\n\
Options -Indexes +FollowSymLinks\n\
AllowOverride All\n\
Require all granted\n\
</Directory>\n" >> /etc/apache2/apache2.conf

# Silence ServerName warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

EXPOSE 80

