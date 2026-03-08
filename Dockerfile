# Use the Apache image instead of FPM
FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y git libzip-dev default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql zip bcmath

# Change Apache document root to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Enable Apache mod_rewrite for Laravel routing
RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --no-interaction --optimize-autoloader
RUN chown -R www-data:www-data storage bootstrap/cache

# Remove the entrypoint script entirely and let Apache handle the serving
# You can run migrations in the Render dashboard under "Build Command" or "Start Command" instead.
