#!/bin/bash

# Set permissions first
chmod -R 777 /var/www/html/storage
chmod -R 777 /var/www/html/bootstrap/cache

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run migrations only (không seed mỗi lần deploy)
php artisan migrate --force || echo "Migration failed, continuing..."

# Start Apache
apache2-foreground
