#!/bin/bash
set -e

# Link storage if not linked
php artisan storage:link || true

# Optimize for production
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations safely
# This will only create missing tables/columns, it will not delete data
php artisan migrate --force

# Execute the main container command
exec "$@"
