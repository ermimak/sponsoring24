<?php

/**
 * Script to update all migration files for MySQL UUID compatibility
 * This script replaces all $table->uuid() calls with $table->char(36)
 */

$migrationPath = __DIR__ . '/database/migrations/';
$files = glob($migrationPath . '*.php');

$replacements = [
    // Primary UUID columns
    '$table->uuid(\'id\')->primary()' => '$table->char(\'id\', 36)->primary()',
    
    // Foreign key UUID columns - common patterns
    '$table->uuid(\'user_id\')' => '$table->char(\'user_id\', 36)',
    '$table->uuid(\'project_id\')' => '$table->char(\'project_id\', 36)',
    '$table->uuid(\'participant_id\')' => '$table->char(\'participant_id\', 36)',
    '$table->uuid(\'supporter_id\')' => '$table->char(\'supporter_id\', 36)',
    '$table->uuid(\'member_group_id\')' => '$table->char(\'member_group_id\', 36)',
    '$table->uuid(\'permission_id\')' => '$table->char(\'permission_id\', 36)',
    '$table->uuid(\'role_id\')' => '$table->char(\'role_id\', 36)',
    '$table->uuid(\'team_id\')' => '$table->char(\'team_id\', 36)',
    '$table->uuid(\'tokenable_id\')' => '$table->char(\'tokenable_id\', 36)',
    '$table->uuid(\'created_by\')' => '$table->char(\'created_by\', 36)',
    '$table->uuid(\'referrer_id\')' => '$table->char(\'referrer_id\', 36)',
    
    // Generic UUID pattern
    '/\$table->uuid\(([^)]+)\)/' => '$table->char($1, 36)',
];

$updatedFiles = [];

foreach ($files as $file) {
    $content = file_get_contents($file);
    $originalContent = $content;
    
    // Apply string replacements first
    foreach ($replacements as $search => $replace) {
        if (strpos($search, '/') === 0) {
            // Regex replacement
            $content = preg_replace($search, $replace, $content);
        } else {
            // String replacement
            $content = str_replace($search, $replace, $content);
        }
    }
    
    // If content changed, write back to file
    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        $updatedFiles[] = basename($file);
        echo "Updated: " . basename($file) . "\n";
    }
}

echo "\nTotal files updated: " . count($updatedFiles) . "\n";
if (!empty($updatedFiles)) {
    echo "Updated files:\n";
    foreach ($updatedFiles as $file) {
        echo "- $file\n";
    }
}

echo "\nMySQL migration update completed!\n";
