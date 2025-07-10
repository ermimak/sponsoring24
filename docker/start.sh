#!/bin/bash

echo "Setting up Laravel..."

# Create js directory if it doesn't exist
mkdir -p /var/www/html/public/js

# Ensure .env exists
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
        if [ -n "$APP_KEY" ]; then
            sed -i "s/APP_KEY=/APP_KEY=$APP_KEY/" .env
        fi
    else
        echo "Creating default .env file..."
        cat > .env << EOF
APP_NAME=Sponsoring24
APP_ENV=production
APP_KEY=$APP_KEY
APP_DEBUG=false
APP_URL=https://sponsoring24.onrender.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=${MAIL_MAILER:-smtp}
MAIL_HOST=${MAIL_HOST:-mailhog}
MAIL_PORT=${MAIL_PORT:-1025}
MAIL_USERNAME=${MAIL_USERNAME:-null}
MAIL_PASSWORD=${MAIL_PASSWORD:-null}
MAIL_ENCRYPTION=${MAIL_ENCRYPTION:-null}
MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS:-"hello@sponsoring24.com"}
MAIL_FROM_NAME=${MAIL_FROM_NAME:-"Sponsoring24"}

STRIPE_KEY=${STRIPE_KEY}
STRIPE_SECRET=${STRIPE_SECRET}
STRIPE_WEBHOOK_SECRET=${STRIPE_WEBHOOK_SECRET}

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="Sponsoring24"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
EOF
    fi
fi

# Run composer dump-autoload to ensure all classes are available
composer dump-autoload --optimize

# Check Vite manifest
if [ ! -f /var/www/html/public/build/manifest.json ]; then
    echo "Warning: Vite manifest.json not found, creating empty one"
    mkdir -p /var/www/html/public/build
    echo "{}" > /var/www/html/public/build/manifest.json
fi

# Run Laravel setup commands
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
else
    echo "APP_KEY is set, skipping key generation"
fi

# Set up storage and cache directories
php artisan storage:link

# Generate Ziggy routes and ensure they are in the correct location
php artisan ziggy:generate
if [ -f resources/js/ziggy.js ]; then
    cp resources/js/ziggy.js public/js/ziggy.js
fi

# Set proper permissions
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/public/js
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/public/js

# Wait for database to be ready with timeout
echo "Checking database connection..."
DB_READY=false
COUNTER=0
MAX_TRIES=30

while [ $COUNTER -lt $MAX_TRIES ]; do
    if pg_isready -h "${DB_HOST}" -p "${DB_PORT}" -U "${DB_USERNAME}" -d "${DB_DATABASE}" > /dev/null 2>&1; then
        DB_READY=true
        echo "Database is up - executing migrations and seeding"
        # Run migrations and seed the database
        php artisan migrate:fresh --seed --force
        break
    fi
    echo "Database is unavailable - attempt $COUNTER of $MAX_TRIES - sleeping"
    COUNTER=$((COUNTER+1))
    sleep 2
done

if [ "$DB_READY" = false ]; then
    echo "WARNING: Database connection could not be established after $MAX_TRIES attempts"
    echo "The application will start in degraded mode without database connectivity"
    # Create a flag file to indicate degraded mode
    touch /var/www/html/storage/framework/down_db
    
    # Create a custom error handler for database connection issues
    cat > /var/www/html/app/Exceptions/DatabaseUnavailableException.php << "EOF"
<?php

namespace App\Exceptions;

use Exception;

class DatabaseUnavailableException extends Exception
{
    //
}
EOF

    # Update the exception handler to catch database connection issues
    if [ -f /var/www/html/app/Exceptions/Handler.php ]; then
        # Add database unavailable check to the Handler.php
        sed -i "/use Throwable;/a use App\\\\Exceptions\\\\DatabaseUnavailableException;\nuse Illuminate\\\\Database\\\\QueryException;" /var/www/html/app/Exceptions/Handler.php
        sed -i "/public function register(/a \\        \$this->reportable(function (QueryException \$e) {\\n            if (file_exists(storage_path('framework/down_db'))) {\\n                throw new DatabaseUnavailableException('Database connection failed. The application is running in degraded mode.');\\n            }\\n        });" /var/www/html/app/Exceptions/Handler.php
    fi
fi

# Set up Laravel scheduler
echo "* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1" > /etc/cron.d/laravel-scheduler
chmod 0644 /etc/cron.d/laravel-scheduler

# Clear and rebuild Laravel caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start supervisor for Laravel scheduler and queue workers
service supervisor start

# Ensure supervisor is running our Laravel processes
supervisorctl reread
supervisorctl update
supervisorctl start laravel-scheduler
supervisorctl start laravel-queue

# Fix storage directory permissions
echo "Fixing storage directory permissions..."

# Set correct permissions for Laravel storage directories
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

# Clean up old build artifacts and ensure fresh assets
echo "Cleaning up old build artifacts and ensuring fresh assets..."

# Run fix-permissions script
/usr/local/bin/fix-permissions.sh

# Check if we're running on Render
if [ -n "$RENDER" ] || [ -n "$RENDER_EXTERNAL_URL" ]; then
    echo "💶 Detected Render environment, using special build script..."
    /usr/local/bin/render-build.sh
    
    # Use Render-specific Nginx configuration with dynamic port binding
    echo "🚀 Configuring Nginx for Render with PORT=${PORT:-10000}..."
    
    # Create Nginx configuration dynamically
    cat > /etc/nginx/conf.d/default.conf << EOF
server {
    listen 0.0.0.0:${PORT:-10000};
    server_name _;
    root /var/www/html/public;
    index index.php;

    charset utf-8;
    client_max_body_size 100M;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF
    
    # Log the Nginx configuration for debugging
    echo "📑 Nginx configuration:"
    cat /etc/nginx/conf.d/default.conf
else
    # Run standard clean-build script
    echo "💶 Using standard build script..."
    /usr/local/bin/build-vite.sh
fi

# Verify manifest.json exists and has proper permissions
if [ -f "/var/www/html/public/build/manifest.json" ]; then
    echo "✅ Vite manifest.json verified"
    chmod 644 /var/www/html/public/build/manifest.json
    
    # Display manifest content for debugging
    echo "📄 Manifest content:"
    cat /var/www/html/public/build/manifest.json
else
    echo "❌ Failed to create Vite manifest.json"
    echo "⚠️ Warning: Vite manifest.json still not found after build"
    
    # Create a basic manifest as a fallback
    echo "⚠️ Creating basic manifest as fallback"
    mkdir -p /var/www/html/public/build
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
}' > /var/www/html/public/build/manifest.json
    chmod 644 /var/www/html/public/build/manifest.json
    
    # Create assets directory and basic files
    mkdir -p /var/www/html/public/build/assets
    echo "/* Fallback CSS */" > /var/www/html/public/build/assets/app.css
    echo "console.log('Fallback JS');" > /var/www/html/public/build/assets/app.js
    chmod 644 /var/www/html/public/build/assets/app.css
    chmod 644 /var/www/html/public/build/assets/app.js
fi

# Ensure proper permissions on all built assets
chown -R www-data:www-data /var/www/html/public/build
chmod -R 775 /var/www/html/public/build


# Check if we're running on Render
if [ -n "$RENDER" ] || [ -n "$RENDER_EXTERNAL_URL" ]; then
    echo "🚀 Running on Render, configuring for port ${PORT:-10000}..."
    
    # Configure Nginx to listen on the PORT environment variable
    cat > /etc/nginx/conf.d/default.conf << EOF
server {
    listen ${PORT:-10000} default_server;
    listen [::]:${PORT:-10000} default_server;
    server_name _;
    root /var/www/html/public;
    index index.php;

    charset utf-8;
    client_max_body_size 100M;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

    # Create a health check endpoint
=======
# Start PHP-FPM
echo "Starting PHP-FPM..."
php-fpm -D

# Check if we're running on Render
if [ -n "$RENDER" ] || [ -n "$RENDER_EXTERNAL_URL" ]; then
    echo "🔍 Running on Render, ensuring port ${PORT:-10000} is properly bound..."
    
    # Create a simple health check endpoint for Render
    mkdir -p /var/www/html/public/health
    echo '<?php echo "OK"; ?>' > /var/www/html/public/health/index.php
    chmod 644 /var/www/html/public/health/index.php
    
    echo "📡 Nginx configured to listen on port ${PORT:-10000}"
    cat /etc/nginx/conf.d/default.conf
fi

# Start PHP-FPM
echo "Starting PHP-FPM..."
php-fpm -D

# Start Nginx in foreground mode
echo "Starting Nginx..."
exec nginx -g "daemon off;"
=======
    # Log port binding information
    echo "📡 Binding to port ${PORT:-10000} for Render..."
    
    # Start Nginx in foreground mode to keep container running
    echo "🚀 Starting Nginx on port ${PORT:-10000}..."
    exec nginx -g "daemon off;"
else
    # Start Nginx normally for local development
    echo "Starting Nginx..."
    nginx -g "daemon off;"
    
    echo "Nginx started. PHP-FPM is running."
    # Keep container running and show logs
    tail -f /var/log/nginx/error.log /var/log/nginx/access.log /var/www/html/storage/logs/laravel.log
fi

