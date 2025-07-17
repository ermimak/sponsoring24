# Stage 1: Build frontend assets
# This stage uses a Node.js image to build your assets.
FROM node:20-alpine as frontend-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: Final PHP application image
# This is your main application image. It no longer needs Node.js.
FROM php:8.2-fpm

# Install system dependencies (Node.js is removed)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    postgresql-client \
    cron \
    supervisor \
    libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip \
    && docker-php-ext-enable opcache

# Install additional PHP extensions that might be needed
RUN pecl install redis-5.3.7 \
    && docker-php-ext-enable redis

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files and install dependencies
COPY database/ database/
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --no-dev

# Copy application code
COPY . .

# Copy built assets from the frontend-builder stage
COPY --from=frontend-builder /app/public/build ./public/build

# Finish composer install
RUN composer install --optimize-autoloader --no-dev

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents with proper ownership
COPY --chown=www-data:www-data . /var/www/html

# Copy Docker scripts to /usr/local/bin and make them executable
COPY docker/build-vite.sh /usr/local/bin/
COPY docker/start.sh /usr/local/bin/
COPY docker/fix-permissions.sh /usr/local/bin/
COPY docker/clean-build.sh /usr/local/bin/
COPY render-build.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/build-vite.sh \
    && chmod +x /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/fix-permissions.sh \
    && chmod +x /usr/local/bin/clean-build.sh \
    && chmod +x /usr/local/bin/render-build.sh

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

# Install PHP dependencies only (Node/NPM handled by separate service)
RUN composer install --no-dev --optimize-autoloader

# Generate Ziggy routes for Vue.js
RUN if [ -f artisan ]; then php artisan ziggy:generate; fi

# Create directory for Ziggy in node_modules (will be used by node service)
RUN mkdir -p /var/www/html/node_modules/ziggy

# Remove any old build artifacts directory if it exists
RUN rm -rf /var/www/html/build-artifacts

# Create supervisor configuration for Laravel scheduler and queue
RUN mkdir -p /etc/supervisor/conf.d /var/log/supervisor
RUN echo '[program:laravel-scheduler]\n\
command=/bin/bash -c "while [ true ]; do php /var/www/html/artisan schedule:run --verbose --no-interaction; sleep 60; done"\n\
autostart=true\n\
autorestart=true\n\
user=www-data\n\
redirect_stderr=true\n\
stdout_logfile=/var/log/supervisor/scheduler.log\n\
stopwaitsecs=60\n\
\n\
[program:laravel-queue]\n\
command=php /var/www/html/artisan queue:work --tries=3 --timeout=90\n\
autostart=true\n\
autorestart=true\n\
user=www-data\n\
redirect_stderr=true\n\
stdout_logfile=/var/log/supervisor/queue.log\n\
stopwaitsecs=60' > /etc/supervisor/conf.d/laravel.conf

# Copy PHP configuration
COPY docker/php/production.ini /usr/local/etc/php/conf.d/99-production.ini

# We'll create the Nginx configuration dynamically at runtime in start.sh

# Copy startup script
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Expose port 80 and Render's dynamic port (default 10000)
EXPOSE 80 10000
# Note: Render will use the PORT environment variable (default: 10000)

# Start Nginx and PHP-FPM
CMD ["/usr/local/bin/start.sh"]
