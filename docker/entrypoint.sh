#!/bin/sh
set -e

# Link storage if not linked
php artisan storage:link

# Clear config and optimize for production
php artisan optimize

# Execute the main container command
exec "$@"
