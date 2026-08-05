#!/bin/bash
set -e

echo "Starting Laravel deployment..."

php artisan storage:link || true

# Create database
php artisan migrate:fresh --force

# Create admin user
php artisan db:seed --force || true

# Clear cache
php artisan optimize:clear

# Cache config only
php artisan config:cache

echo "Laravel deployment completed successfully."

exec "$@"
