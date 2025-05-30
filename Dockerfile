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
RUN mkdir -p /etc/nginx/conf.d && \
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
    location / {\n\
        try_files $uri $uri/ /index.php?$query_string;\n\
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
    }\n\
\n\
    location ~ /\.(?!well-known).* {\n\
        deny all;\n\
    }\n\
}' > /etc/nginx/conf.d/default.conf

# Install dependencies and build assets
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Create startup script with debugging
RUN echo '#!/bin/bash\n\
echo "Starting Nginx..."\n\
nginx -t\n\
service nginx start\n\
echo "Nginx started. Starting PHP-FPM..."\n\
php-fpm\n\
echo "PHP-FPM started. Both services are running."\n\
# Keep container running and show logs\n\
tail -f /var/log/nginx/error.log /var/log/nginx/access.log' > /usr/local/bin/start.sh && \
chmod +x /usr/local/bin/start.sh

# Expose port 80
EXPOSE 80

# Start Nginx and PHP-FPM
CMD ["/usr/local/bin/start.sh"]