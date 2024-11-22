FROM php:8.0-apache

WORKDIR /var/www/html

# Install mysqli extension
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy custom Apache configuration
COPY apache-config.conf /etc/apache2/conf-available/
RUN a2enconf apache-config

# Copy application files
COPY src/ /var/www/html/

# # Set correct permissions
# RUN chown -R www-data:www-data /var/www/html \
#     && chmod -R 755 /var/www/html

# Update and upgrade packages
RUN apt-get update -y && apt-get upgrade -y