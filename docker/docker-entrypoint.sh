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

echo "Building production caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo " Creating storage symlink..."
rm -rf public/storage 2>/dev/null || true
php artisan storage:link --force || true

echo " Setting runtime permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Run database operations only for web container
if [[ "$1" == "apache2-foreground"* ]]; then
    echo "Running database migrations..."
    php artisan migrate --force

    echo " Running database seeders..."
    php artisan db:seed --force
fi

echo " Laravel deployment completed successfully."

exec "$@"
