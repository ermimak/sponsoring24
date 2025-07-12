#!/bin/bash

# This script is specifically for Render.com deployment
# It ensures Vite is properly installed and assets are built correctly

# Enable error reporting but disable verbose debugging
set -e

echo "Starting Render build process..."

# Ensure we're in the project root
cd /var/www/html || exit 1

# Assets are pre-built and included in the repository.
# This script now only needs to ensure permissions are correct.

echo "Verifying build assets..."
if [ ! -f "public/build/manifest.json" ]; then
    echo " ERROR: public/build/manifest.json not found!"
    echo "Please build assets locally and commit the public/build directory."
    exit 1
fi

# Set proper permissions for storage and bootstrap cache
echo " Setting directory permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Set proper permissions for the build directory
chmod -R 775 public/build

echo " Render build process completed"
