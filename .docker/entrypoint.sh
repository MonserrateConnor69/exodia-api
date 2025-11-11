#!/bin/sh
set -e # Exit immediately if a command exits with a non-zero status.

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Start supervisord
echo "Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf