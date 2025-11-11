#!/bin/sh

# Exit immediately if a command exits with a non-zero status.
set -e

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Start the Laravel server. Render provides the PORT variable.
echo "Starting Laravel server..."
php artisan serve --host 0.0.0.0 --port ${PORT}