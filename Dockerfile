# Use an official PHP image as a base
FROM php:8.2-fpm-alpine

# Install dependencies needed for Laravel
RUN apk add --no-cache git libzip-dev postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql zip bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /var/www/html

# Copy composer files and install vendors
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Copy the rest of the application code
COPY . .

# Set correct permissions for Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# Copy the entrypoint script and make it executable
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set the entrypoint to our script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]