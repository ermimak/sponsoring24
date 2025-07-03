#!/bin/bash

# Script to ensure fresh frontend assets are built and served
# This script is meant to be run during deployment or container startup

echo "🧹 Cleaning up old build artifacts..."
if [ -d "/var/www/html/build-artifacts" ]; then
    rm -rf /var/www/html/build-artifacts
    echo "✅ Removed old build-artifacts directory"
else
    echo "ℹ️ No build-artifacts directory found, skipping"
fi

# Ensure public/build directory exists
mkdir -p /var/www/html/public/build

# If we're in development mode, run npm install and build
if [ "${APP_ENV}" = "local" ] || [ "${APP_ENV}" = "development" ]; then
    echo "🔧 Development environment detected, installing npm dependencies..."
    npm ci
    
    echo "🔨 Building fresh assets..."
    npm run build
    
    echo "✅ Fresh assets built successfully"
else
    echo "🏭 Production environment detected, assuming assets were built during image creation"
fi

# Generate Ziggy routes for Vue.js
echo "🔄 Generating Ziggy routes..."
php artisan ziggy:generate

echo "✅ Asset preparation complete"
