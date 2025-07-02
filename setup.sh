#!/bin/bash

# Build and start containers
docker compose up -d --build

# Wait for containers to be ready
sleep 10

# Create Laravel project
docker compose exec -T app composer create-project --prefer-dist laravel/laravel:^12.0 .

# Set proper permissions
docker compose exec -T app chown -R dev:dev /var/www

# Install dependencies
docker compose exec -T app composer install

# Generate application key
docker compose exec -T app php artisan key:generate

# Set proper permissions for storage and cache
docker compose exec -T app chmod -R 775 storage bootstrap/cache

echo "Setup completed! Your Laravel application is ready at https://sponsoring24.onrender.com" 