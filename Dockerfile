# Use an official PHP image as a base
FROM php:8.2-fpm-alpine

# Install system dependencies, git (for composer), and required PHP extensions
RUN apk add --no-cache git libzip-dev postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql zip bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /var/www/html

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Copy the rest of the application code
COPY . .

# Set permissions for Laravel's storage and cache
RUN chown -R www-data:www-data storage bootstrap/cache