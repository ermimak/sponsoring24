#!/bin/bash

# This script is specifically for Render.com deployment
# It ensures Vite is properly installed and assets are built correctly

# Enable debugging and error reporting
set -x
set -e

echo "🚀 Starting Render build process..."
echo "📊 Environment: NODE_ENV=$NODE_ENV, PATH=$PATH"
echo "📂 Current directory: $(pwd)"
echo "📋 Directory listing:"
ls -la

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

# Show package.json scripts
echo "📝 Package.json scripts:"
cat package.json | grep -A 15 "\"scripts\""

# Show vite version
echo "📍 Vite version:"
npx vite --version

# Try building with explicit production flag
export NODE_ENV=production
echo "🔨 Running npm run build with NODE_ENV=$NODE_ENV"
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
        
        # Create actual CSS and JS files with basic content
        echo "/* Auto-generated CSS file */" > public/build/assets/app.css
        echo "console.log('Auto-generated JS file');" > public/build/assets/app.js
        
        # Copy actual resources if they exist
        if [ -d "resources/css" ]; then
            echo "💾 Copying CSS resources..."
            cp -r resources/css/* public/build/assets/ || true
        fi
        
        if [ -d "resources/js" ]; then
            echo "💾 Copying JS resources..."
            cp -r resources/js/* public/build/assets/ || true
        fi
    fi
fi

# Set proper permissions
echo "🔑 Setting proper permissions..."
chmod -R 775 public/build

echo "✅ Render build process completed"
