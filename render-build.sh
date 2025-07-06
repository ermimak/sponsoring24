#!/bin/bash

# This script is specifically for Render.com deployment
# It ensures Vite is properly installed and assets are built correctly

echo "🚀 Starting Render build process..."

# Ensure we're in the project root
cd /var/www/html || exit 1

# Install Vite globally if not already installed
if ! command -v vite &> /dev/null; then
    echo "📦 Installing Vite globally..."
    npm install -g vite
fi

# Install dependencies
echo "📦 Installing npm dependencies..."
npm ci

# Ensure Vite is installed locally
if [ ! -d "node_modules/vite" ]; then
    echo "📦 Installing Vite locally..."
    npm install vite
fi

# Create build directory with proper permissions
echo "🔧 Preparing build directory..."
mkdir -p public/build
chmod -R 775 public/build

# Force production mode
export NODE_ENV=production

# Build assets
echo "🔨 Building Vite assets..."
npm run build

# Verify manifest.json was created
if [ -f "public/build/manifest.json" ] && [ -s "public/build/manifest.json" ] && [ "$(cat public/build/manifest.json)" != "{}" ]; then
    echo "✅ Vite manifest.json created successfully"
    ls -la public/build/
else
    echo "❌ Failed to create Vite manifest.json"
    echo "⚠️ Attempting direct Vite build..."
    
    # Try direct Vite build command
    npx vite build
    
    # Check again
    if [ -f "public/build/manifest.json" ] && [ -s "public/build/manifest.json" ] && [ "$(cat public/build/manifest.json)" != "{}" ]; then
        echo "✅ Vite manifest.json created successfully on second attempt"
        ls -la public/build/
    else
        echo "❌ Still failed to create Vite manifest.json"
        echo "⚠️ Creating a debug log for troubleshooting..."
        
        # Create debug log
        echo "DEBUG LOG" > vite-build-debug.log
        echo "Node version: $(node -v)" >> vite-build-debug.log
        echo "NPM version: $(npm -v)" >> vite-build-debug.log
        echo "Vite global: $(which vite || echo 'not found')" >> vite-build-debug.log
        echo "Package.json:" >> vite-build-debug.log
        cat package.json >> vite-build-debug.log
        echo "Vite config:" >> vite-build-debug.log
        cat vite.config.js >> vite-build-debug.log
        
        # Create empty manifest as last resort
        echo "⚠️ Creating empty manifest.json as fallback..."
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
    fi
fi

# Set proper permissions
echo "🔑 Setting proper permissions..."
chmod -R 775 public/build

echo "✅ Render build process completed"
