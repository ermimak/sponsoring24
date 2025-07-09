<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Production Environment Specific Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration settings specific to the production
    | environment on Render.com.
    |
    */

    // Enable error reporting for debugging
    'debug_mode' => env('PRODUCTION_DEBUG', false),
    
    // Asset configuration
    'assets' => [
        'use_manifest' => env('USE_VITE_MANIFEST', true),
        'fallback_css' => '/build/assets/app.css',
        'fallback_js' => '/build/assets/app.js',
    ],
    
    // Logging configuration
    'logging' => [
        'detailed' => env('PRODUCTION_DETAILED_LOGGING', true),
        'path' => storage_path('logs/production.log'),
    ],
];
