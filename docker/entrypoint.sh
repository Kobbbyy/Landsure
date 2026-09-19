#!/bin/sh
set -e

echo "Clearing stale config..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "Running migrations..."
php artisan migrate --force

echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Substitute PORT into the Nginx config before starting Nginx
echo "Configuring Nginx to listen on port ${PORT}..."
envsubst '${PORT}' < /etc/nginx/sites-available/default > /etc/nginx/sites-enabled/default

echo "Starting supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf