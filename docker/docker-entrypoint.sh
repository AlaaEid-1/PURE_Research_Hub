#!/bin/bash
set -e

echo "Starting Laravel deployment..."

php artisan storage:link || true

php artisan migrate --force

php artisan db:seed --force || true

php artisan optimize:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache || true

echo "Laravel deployment completed successfully."

exec "$@"
