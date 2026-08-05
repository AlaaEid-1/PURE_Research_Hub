#!/bin/bash
set -e

echo " Starting Laravel deployment..."

# Check Laravel installation
if [ ! -f "artisan" ]; then
    echo " Laravel artisan file not found."
    exit 1
fi

echo " Clearing old caches..."
php artisan optimize:clear || true


# Run database operations only for web container
if [[ "$1" == "apache2-foreground"* ]]; then

    echo "Running database migrations..."
    php artisan migrate --force

    echo " Running database seeders..."
    php artisan db:seed --force

fi


echo " Creating storage symlink..."
php artisan storage:link || true


echo "Building production caches..."

php artisan config:cache
php artisan route:cache
php artisan view:cache


echo " Laravel deployment completed successfully."

exec "$@"
