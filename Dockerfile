# Stage 1: Install dependencies with Composer
FROM composer:2 as vendor

WORKDIR /app
COPY database/ database/
COPY composer.json composer.json
COPY composer.lock composer.lock
RUN composer install --ignore-platform-reqs --no-interaction --no-plugins --no-scripts --prefer-dist

# Stage 2: Build the application image
FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    libzip-dev \
    zip \
    postgresql-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql zip

# Copy application code
COPY . .

# Copy vendor files from the first stage
COPY --from=vendor /app/vendor/ vendor/

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy Nginx and Supervisor configurations
# You will need to create these config files (see below)
COPY ./.docker/nginx.conf /etc/nginx/nginx.conf
COPY ./.docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port 8000 for the web server
EXPOSE 8000

# Entrypoint script to run migrations and start services
COPY ./.docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]