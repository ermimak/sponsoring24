#!/bin/bash

# Script to fix Laravel storage directory permissions
# This should be run during container startup

echo "🔑 Setting correct permissions for Laravel storage directories..."

# Set ownership to www-data (web server user)
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache

# Set directory permissions to 775 (drwxrwxr-x)
find /var/www/html/storage -type d -exec chmod 775 {} \;
find /var/www/html/bootstrap/cache -type d -exec chmod 775 {} \;

# Set file permissions to 664 (rw-rw-r--)
find /var/www/html/storage -type f -exec chmod 664 {} \;
find /var/www/html/bootstrap/cache -type f -exec chmod 664 {} \;

# Ensure log directory exists and has correct permissions
mkdir -p /var/www/html/storage/logs
chmod 775 /var/www/html/storage/logs
touch /var/www/html/storage/logs/laravel.log
chmod 664 /var/www/html/storage/logs/laravel.log
chown www-data:www-data /var/www/html/storage/logs/laravel.log

# Ensure public/build directory exists and has correct permissions
echo "🔑 Setting correct permissions for Vite build directory..."
mkdir -p /var/www/html/public/build
chown -R www-data:www-data /var/www/html/public/build
chmod -R 775 /var/www/html/public/build

# Create empty manifest.json if it doesn't exist
if [ ! -f "/var/www/html/public/build/manifest.json" ]; then
    echo "⚠️ Creating empty Vite manifest.json..."
    echo '{}' > /var/www/html/public/build/manifest.json
    chown www-data:www-data /var/www/html/public/build/manifest.json
    chmod 664 /var/www/html/public/build/manifest.json
fi

echo "✅ Permissions set successfully"
