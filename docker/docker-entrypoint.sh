#!/bin/bash
set -e

echo "Starting Laravel deployment..."

# Clear old caches first
php artisan optimize:clear || true

# Build production caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage symlink
php artisan storage:link || true

# Run database operations only for web container
if [[ "$1" == "apache2-foreground"* ]]; then

    echo "Running database migrations..."
    php artisan migrate --force

    echo "Running database seeders..."
    php artisan db:seed --force

fi

echo "Laravel deployment completed successfully."

exec "$@"
