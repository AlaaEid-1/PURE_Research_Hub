#!/bin/bash
set -e

echo "Starting Laravel deployment..."

# Create storage symlink
php artisan storage:link || true

# Run database migrations first
# Creates tables like cache, jobs, sessions, etc.
php artisan migrate --force

# Run seeders safely (for admin/default data)
php artisan db:seed --force || true

# Clear old caches after database is ready
php artisan optimize:clear

# Cache application files for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Laravel deployment completed successfully."

# Execute the main container command
exec "$@"
