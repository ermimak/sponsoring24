<?php
// Debug file to check PHP and environment status on production

// Enable all error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>PHP Environment Debug</h1>";

// Basic PHP info
echo "<h2>PHP Version</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Check extensions
echo "<h2>PHP Extensions</h2>";
echo "<ul>";
$extensions = get_loaded_extensions();
sort($extensions);
foreach ($extensions as $extension) {
    echo "<li>$extension</li>";
}
echo "</ul>";

// Environment variables
echo "<h2>Environment Variables</h2>";
echo "<pre>";
print_r($_ENV);
echo "</pre>";

// Directory permissions
echo "<h2>Directory Permissions</h2>";
$directories = [
    '/var/www/html/public',
    '/var/www/html/public/build',
    '/var/www/html/storage',
    '/var/www/html/storage/logs',
    '/var/www/html/storage/framework',
    '/var/www/html/bootstrap/cache'
];

echo "<ul>";
foreach ($directories as $dir) {
    if (file_exists($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        $owner = posix_getpwuid(fileowner($dir))['name'];
        $group = posix_getgrgid(filegroup($dir))['name'];
        echo "<li>$dir - Permissions: $perms, Owner: $owner, Group: $group</li>";
    } else {
        echo "<li>$dir - Directory does not exist</li>";
    }
}
echo "</ul>";

// Check Vite manifest
echo "<h2>Vite Manifest</h2>";
$manifestPath = '/var/www/html/public/build/manifest.json';
if (file_exists($manifestPath)) {
    echo "<p>Manifest exists</p>";
    echo "<pre>";
    echo htmlspecialchars(file_get_contents($manifestPath));
    echo "</pre>";
} else {
    echo "<p>Manifest does not exist at $manifestPath</p>";
}

// Check Laravel logs
echo "<h2>Recent Laravel Logs</h2>";
$logPath = '/var/www/html/storage/logs/laravel.log';
if (file_exists($logPath)) {
    echo "<p>Log file exists</p>";
    echo "<pre>";
    $logs = shell_exec("tail -n 50 $logPath");
    echo htmlspecialchars($logs);
    echo "</pre>";
} else {
    echo "<p>Log file does not exist at $logPath</p>";
}

// Check Laravel storage structure
echo "<h2>Laravel Storage Structure</h2>";
echo "<pre>";
echo shell_exec("ls -la /var/www/html/storage");
echo "</pre>";

// Check if .env file exists
echo "<h2>.env File</h2>";
if (file_exists('/var/www/html/.env')) {
    echo "<p>.env file exists</p>";
} else {
    echo "<p>.env file does not exist</p>";
}

// Check Laravel cache
echo "<h2>Laravel Cache</h2>";
echo "<pre>";
echo shell_exec("ls -la /var/www/html/bootstrap/cache");
echo "</pre>";
