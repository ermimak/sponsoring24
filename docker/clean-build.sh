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

# Check if node_modules exists and install if needed
if [ ! -d "node_modules" ] || [ ! -f "node_modules/.package-lock.json" ]; then
    echo "🔧 Installing npm dependencies..."
    npm ci
else
    # Check if vite is installed locally
    if [ ! -d "node_modules/vite" ]; then
        echo "🔧 Installing Vite locally..."
        npm install vite
    fi
fi

# Check if manifest.json exists and has content, if not, build the assets
if [ ! -f "/var/www/html/public/build/manifest.json" ] || [ ! -s "/var/www/html/public/build/manifest.json" ] || [ "$(cat /var/www/html/public/build/manifest.json)" = "{}" ]; then
    echo "⚠️ No valid manifest.json found, building fresh assets..."
    # Force NODE_ENV to production for optimal build
    export NODE_ENV=production
    
    # Check if vite is available globally
    if ! command -v vite &> /dev/null; then
        echo "⚠️ Vite not found globally, installing..."
        npm install -g vite
    fi
    
    # Run the build
    npm run build
    
    # Verify build was successful
    if [ -f "/var/www/html/public/build/manifest.json" ] && [ -s "/var/www/html/public/build/manifest.json" ] && [ "$(cat /var/www/html/public/build/manifest.json)" != "{}" ]; then
        echo "✅ Fresh assets built successfully"
    else
        echo "⚠️ Warning: Vite manifest.json still not found after build"
        echo "Creating empty manifest.json as fallback..."
        echo '{}' > /var/www/html/public/build/manifest.json
    fi
else
    echo "✅ Valid manifest.json found, using existing assets"
fi

# Ensure proper permissions on built assets
chown -R www-data:www-data /var/www/html/public/build
chmod -R 775 /var/www/html/public/build

# Generate Ziggy routes for Vue.js
echo "🔄 Generating Ziggy routes..."
php artisan ziggy:generate

echo "✅ Asset preparation complete"
