# Build and start containers
docker compose up -d --build

# Wait for containers to be ready
Start-Sleep -Seconds 10

# Create Laravel project in a temporary directory
docker compose exec -T app composer create-project --prefer-dist laravel/laravel:^12.0 /tmp/laravel

# Move Laravel files to the correct location
docker compose exec -T app sh -c "cp -r /tmp/laravel/. /var/www/ && rm -rf /tmp/laravel"

# Install dependencies
docker compose exec -T app composer install

# Generate application key
docker compose exec -T app php artisan key:generate

# Set proper permissions for storage and cache
docker compose exec -T app chmod -R 775 storage bootstrap/cache

Write-Host "Setup completed! Your Laravel application is ready at https://sponsoring24.onrender.com" 