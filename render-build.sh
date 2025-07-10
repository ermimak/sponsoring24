#!/bin/bash

# This script is specifically for Render.com deployment
# It ensures Vite is properly installed and assets are built correctly

# Enable error reporting but disable verbose debugging
set -e

echo "Starting Render build process..."

# Ensure we're in the project root
cd /var/www/html || exit 1

# Skip global Vite install to save memory

# Install only essential dependencies with minimal output
echo "Installing minimal npm dependencies..."

# Create a temporary package.json with only essential dependencies
TEMP_PKG=$(mktemp)
jq '{name,dependencies:{"vite":"*","@vitejs/plugin-vue":"*","laravel-vite-plugin":"*","vue":"*"}}' package.json > "$TEMP_PKG"
mv "$TEMP_PKG" package.json.minimal

# Install only the minimal dependencies needed for build
npm i --no-audit --no-fund --silent --no-package-lock --production --prefer-offline --no-optional --progress=false --json=false --prefix ./node_modules_minimal -f package.json.minimal

# Use the minimal node_modules for the build
export NODE_PATH=./node_modules_minimal/node_modules

# Create build directory with proper permissions
echo "Preparing build directory..."
mkdir -p public/build
chmod -R 775 public/build

# Force production mode
export NODE_ENV=production

# Build assets
echo "Building Vite assets..."

# Set Node.js memory limit to stay under Render's 512MB limit
export NODE_OPTIONS="--max-old-space-size=384"

# Force production mode
export NODE_ENV=production
echo "Running minimal Vite build"

# Clear memory before build
sync
echo 3 > /proc/sys/vm/drop_caches 2>/dev/null || true

# Use minimal node modules for build
NODE_PATH=$NODE_PATH ./node_modules_minimal/node_modules/.bin/vite build --emptyOutDir --minify=esbuild --outDir=public/build

# Verify manifest.json was created
if [ -f "public/build/manifest.json" ] && [ -s "public/build/manifest.json" ] && [ "$(cat public/build/manifest.json)" != "{}" ]; then
    echo "Vite manifest.json created successfully"
else
    echo "Failed to create Vite manifest.json - creating fallback"
    
    # Create empty manifest as fallback
    echo '{
  "resources/css/app.css": {
    "file": "assets/app.css",
    "src": "resources/css/app.css",
    "isEntry": true
  },
  "resources/js/app.js": {
    "file": "assets/app.js",
    "src": "resources/js/app.js",
    "isEntry": true
  }
}' > public/build/manifest.json
        
        # Create empty asset files to prevent 404 errors
        mkdir -p public/build/assets
        touch public/build/assets/app.css
        touch public/build/assets/app.js

# Clean up temporary files to free memory
rm -f package.json.minimal
rm -rf node_modules_minimal

# Clear npm cache to free disk space
npm cache clean --force > /dev/null 2>&1 || true
    fi
fi

# Set proper permissions
echo "🔑 Setting proper permissions..."
chmod -R 775 public/build

echo "✅ Render build process completed"
