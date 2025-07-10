#!/bin/bash

# This script tests the Nginx configuration with port binding
# to ensure it works correctly on Render

# Set PORT environment variable for testing
export PORT=10000

# Create a test Nginx configuration
echo "Creating test Nginx configuration..."
cat > /tmp/test-nginx.conf << EOF
server {
    listen ${PORT};
    server_name _;
    root /var/www/html/public;
    index index.php;

    location / {
        return 200 'Port binding test successful on port ${PORT}';
    }
}
EOF

echo "Test Nginx configuration:"
cat /tmp/test-nginx.conf

# Check if Nginx is installed for testing
if command -v nginx &> /dev/null; then
    echo "Nginx is installed, can test configuration"
else
    echo "Nginx is not installed, skipping test"
    exit 0
fi

# Test the configuration if possible
if [ -d "/etc/nginx/conf.d" ]; then
    echo "Testing Nginx configuration..."
    sudo cp /tmp/test-nginx.conf /etc/nginx/conf.d/test.conf
    sudo nginx -t
    echo "Reloading Nginx to apply test configuration..."
    sudo nginx -s reload
    echo "Checking if port ${PORT} is open..."
    curl http://localhost:${PORT}
    echo "Removing test configuration..."
    sudo rm /etc/nginx/conf.d/test.conf
    sudo nginx -s reload
else
    echo "Nginx conf.d directory not found, skipping test"
fi

echo "Test complete"
