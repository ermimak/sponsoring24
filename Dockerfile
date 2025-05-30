FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    postgresql-client \
    nginx \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u 1000 -d /home/dev dev
RUN mkdir -p /home/dev/.composer && \
    chown -R dev:dev /home/dev

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . /var/www/html

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www/html

# Create storage directory structure and set permissions
RUN mkdir -p /var/www/html/storage/framework/{sessions,views,cache} \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Configure Nginx
RUN rm -rf /etc/nginx/sites-enabled/default && \
    rm -rf /etc/nginx/sites-available/default && \
    mkdir -p /etc/nginx/conf.d && \
    echo 'server {\n\
    listen 0.0.0.0:80;\n\
    server_name _;\n\
    root /var/www/html/public;\n\
\n\
    add_header X-Frame-Options "SAMEORIGIN";\n\
    add_header X-Content-Type-Options "nosniff";\n\
\n\
    index index.php;\n\
\n\
    charset utf-8;\n\
\n\
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {\n\
        expires max;\n\
        log_not_found off;\n\
        access_log off;\n\
        add_header Cache-Control "public, no-transform";\n\
        try_files $uri =404;\n\
    }\n\
\n\
    location / {\n\
        try_files $uri $uri/ /index.php?$query_string;\n\
        gzip_static on;\n\
    }\n\
\n\
    location = /favicon.ico { access_log off; log_not_found off; }\n\
    location = /robots.txt  { access_log off; log_not_found off; }\n\
\n\
    error_page 404 /index.php;\n\
\n\
    location ~ \.php$ {\n\
        fastcgi_pass 127.0.0.1:9000;\n\
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;\n\
        include fastcgi_params;\n\
        fastcgi_buffers 16 16k;\n\
        fastcgi_buffer_size 32k;\n\
    }\n\
\n\
    location ~ /\.(?!well-known).* {\n\
        deny all;\n\
    }\n\
}' > /etc/nginx/conf.d/default.conf

# Install dependencies and build assets
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build || { echo "npm run build failed"; cat /var/www/html/npm-debug.log; exit 1; }
RUN ls -la /var/www/html/public/build || echo "public/build directory missing"
RUN cat /var/www/html/public/build/manifest.json || echo "manifest.json missing"

# Create startup script with Laravel setup
RUN echo '#!/bin/bash\n\
echo "Setting up Laravel..."\n\
\n\
# Create js directory if it doesn'\''t exist\n\
mkdir -p /var/www/html/public/js\n\
\n\
# Ensure .env exists\n\
if [ ! -f .env ]; then\n\
    if [ -f .env.example ]; then\n\
        cp .env.example .env\n\
    else\n\
        echo "APP_NAME=Laravel\n\
APP_ENV=production\n\
APP_KEY=\n\
APP_DEBUG=false\n\
APP_URL=https://fundoo.onrender.com\n\
\n\
LOG_CHANNEL=stack\n\
LOG_DEPRECATIONS_CHANNEL=null\n\
LOG_LEVEL=debug\n\
\n\
DB_CONNECTION=pgsql\n\
DB_HOST=${DB_HOST}\n\
DB_PORT=${DB_PORT}\n\
DB_DATABASE=${DB_DATABASE}\n\
DB_USERNAME=${DB_USERNAME}\n\
DB_PASSWORD=${DB_PASSWORD}\n\
\n\
BROADCAST_DRIVER=log\n\
CACHE_DRIVER=file\n\
FILESYSTEM_DISK=local\n\
QUEUE_CONNECTION=sync\n\
SESSION_DRIVER=file\n\
SESSION_LIFETIME=120\n\
\n\
MEMCACHED_HOST=127.0.0.1\n\
\n\
REDIS_HOST=127.0.0.1\n\
REDIS_PASSWORD=null\n\
REDIS_PORT=6379\n\
\n\
MAIL_MAILER=smtp\n\
MAIL_HOST=mailpit\n\
MAIL_PORT=1025\n\
MAIL_USERNAME=null\n\
MAIL_PASSWORD=null\n\
MAIL_ENCRYPTION=null\n\
MAIL_FROM_ADDRESS=\"hello@example.com\"\n\
MAIL_FROM_NAME=\"${APP_NAME}\"\n\
\n\
AWS_ACCESS_KEY_ID=\n\
AWS_SECRET_ACCESS_KEY=\n\
AWS_DEFAULT_REGION=us-east-1\n\
AWS_BUCKET=\n\
AWS_USE_PATH_STYLE_ENDPOINT=false\n\
\n\
PUSHER_APP_ID=\n\
PUSHER_APP_KEY=\n\
PUSHER_APP_SECRET=\n\
PUSHER_HOST=\n\
PUSHER_PORT=443\n\
PUSHER_SCHEME=https\n\
PUSHER_APP_CLUSTER=mt1\n\
\n\
VITE_APP_NAME=\"${APP_NAME}\"\n\
VITE_PUSHER_APP_KEY=\"${PUSHER_APP_KEY}\"\n\
VITE_PUSHER_HOST=\"${PUSHER_HOST}\"\n\
VITE_PUSHER_PORT=\"${PUSHER_PORT}\"\n\
VITE_PUSHER_SCHEME=\"${PUSHER_SCHEME}\"\n\
VITE_PUSHER_APP_CLUSTER=\"${PUSHER_APP_CLUSTER}\"\n\
" > .env\n\
    fi\n\
fi\n\
\n\
# Check Vite manifest\n\
if [ ! -f /var/www/html/public/build/manifest.json ]; then\n\
    echo "Error: Vite manifest.json not found"\n\
    exit 1\n\
fi\n\
\n\
# Run Laravel setup commands\n\
php artisan key:generate --force\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
php artisan storage:link\n\
\n\
# Generate Ziggy routes and ensure they are in the correct location\n\
php artisan ziggy:generate\n\
if [ -f resources/js/ziggy.js ]; then\n\
    cp resources/js/ziggy.js public/js/ziggy.js\n\
fi\n\
\n\
# Set proper permissions\n\
chown -R www-data:www-data /var/www/html/storage\n\
chown -R www-data:www-data /var/www/html/bootstrap/cache\n\
chown -R www-data:www-data /var/www/html/public/js\n\
chmod -R 775 /var/www/html/storage\n\
chmod -R 775 /var/www/html/bootstrap/cache\n\
chmod -R 775 /var/www/html/public/js\n\
\n\
# Wait for database to be ready\n\
echo "Waiting for database connection..."\n\
while ! pg_isready -h ${DB_HOST} -p ${DB_PORT} -U ${DB_USERNAME} -d ${DB_DATABASE} > /dev/null 2>&1; do\n\
    echo "Database is unavailable - sleeping"\n\
    sleep 1\n\
done\n\
echo "Database is up - executing migrations"\n\
\n\
# Run migrations\n\
php artisan migrate --force\n\
\n\
echo "Starting Nginx..."\n\
nginx -t\n\
service nginx start\n\
\n\
echo "Nginx started. Starting PHP-FPM..."\n\
php-fpm\n\
\n\
echo "PHP-FPM started. Both services are running."\n\
# Keep container running and show logs\n\
tail -f /var/log/nginx/error.log /var/log/nginx/access.log /var/www/html/storage/logs/laravel.log' > /usr/local/bin/start.sh && \
chmod +x /usr/local/bin/start.sh

# Expose port 80
EXPOSE 80

# Start Nginx and PHP-FPM
CMD ["/usr/local/bin/start.sh"]