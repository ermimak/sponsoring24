#!/bin/bash

# Script to ensure Vite assets are properly built
# This should be run during container startup

echo "🚀 Building Vite assets..."

# Set working directory
cd /var/www/html

# Ensure node_modules exists
if [ ! -d "node_modules" ]; then
    echo "📦 Installing npm dependencies..."
    npm ci
fi

# Clean up old build artifacts
echo "🧹 Cleaning up old build artifacts..."
if [ -d "public/build" ]; then
    rm -rf public/build/*
fi

# Create build directory with proper permissions
mkdir -p public/build
chown -R www-data:www-data public/build
chmod -R 775 public/build

# Build Vite assets
echo "🔨 Building Vite assets..."
export NODE_ENV=production
npm run build

# Verify manifest.json was created
if [ -f "public/build/manifest.json" ]; then
    echo "✅ Vite manifest.json created successfully"
    # Set proper permissions
    chown www-data:www-data public/build/manifest.json
    chmod 664 public/build/manifest.json
else
    echo "❌ Failed to create Vite manifest.json"
    exit 1
fi

echo "✅ Vite build completed successfully"
