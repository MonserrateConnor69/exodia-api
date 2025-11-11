# Use an official PHP image as a base
FROM php:8.2-fpm-alpine

# Install dependencies needed for Laravel
RUN apk add --no-cache git libzip-dev postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql zip bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /var/www/html

# --- THIS IS THE CORRECTED ORDER ---
# 1. Copy ALL application files first
COPY . .

# 2. Now run composer install, with the artisan file present
RUN composer install --no-dev --no-interaction --optimize-autoloader
# --- END CORRECTION ---

# Set correct permissions for Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# Copy the entrypoint script and make it executable
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set the entrypoint to our script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]