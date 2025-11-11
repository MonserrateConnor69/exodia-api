#!/bin/sh
set -e # Exit immediately if a command exits with a non-zero status.

# Clear the config cache to ensure DATABASE_URL is read correctly
echo "Clearing config cache..."
php artisan config:clear

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Start supervisord
echo "Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf