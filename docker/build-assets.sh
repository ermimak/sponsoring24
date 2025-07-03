#!/bin/sh

# Script to build frontend assets for production
# This script ensures Vite is properly installed and available

echo "🔧 Installing dependencies..."
npm install

# Create ziggy directory if it doesn't exist
if [ ! -d /var/www/html/node_modules/ziggy ]; then
  echo "📁 Creating ziggy directory for Vite"
  mkdir -p /var/www/html/node_modules/ziggy
  touch /var/www/html/node_modules/ziggy/vue.m.js
fi

# Remove old build artifacts directory if it exists
echo "🧹 Removing old build artifacts directory if it exists"
rm -rf /var/www/html/build-artifacts

echo "📦 Installing Vite and related packages..."
npm install --save-dev vite @vitejs/plugin-vue laravel-vite-plugin

echo "🔍 Checking for Vite installation..."
if [ -f /var/www/html/node_modules/vite/bin/vite.js ]; then
  echo "✅ Vite found at /var/www/html/node_modules/vite/bin/vite.js"
else
  echo "❌ Vite not found in expected location. Reinstalling..."
  npm install --save-dev vite
  
  if [ ! -f /var/www/html/node_modules/vite/bin/vite.js ]; then
    echo "❌ Failed to install Vite. Aborting."
    exit 1
  fi
fi

echo "🏗️ Building fresh assets to public/build..."
node /var/www/html/node_modules/vite/bin/vite.js build

echo "✅ Asset build complete!"
