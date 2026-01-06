#!/bin/bash

# Set permissions first
chmod -R 777 /var/www/html/storage
chmod -R 777 /var/www/html/bootstrap/cache

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run migrations
php artisan migrate --force || echo "Migration failed, continuing..."

# Run seeder
php artisan db:seed --force || echo "Seeder failed or already seeded"

# Start Apache
apache2-foreground
