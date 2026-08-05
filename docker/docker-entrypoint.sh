#!/bin/bash
set -e

echo "Starting Laravel deployment..."

php artisan storage:link || true

php artisan migrate:fresh --force

php artisan db:seed --force || true

php artisan optimize:clear || true

php artisan config:cache

echo "Laravel deployment completed successfully."

exec "$@"
