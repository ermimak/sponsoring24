<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class HandleViteAssets
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if we're in production and if the Vite manifest exists
        if (app()->environment('production')) {
            $manifestPath = public_path('build/manifest.json');
            
            // Log the manifest status
            if (!file_exists($manifestPath)) {
                Log::warning('Vite manifest.json not found in production', [
                    'path' => $manifestPath,
                    'public_dir_exists' => is_dir(public_path()),
                    'build_dir_exists' => is_dir(public_path('build')),
                ]);
                
                // Create fallback manifest if it doesn't exist
                $this->createFallbackManifest();
            } else {
                // Check if manifest is valid JSON
                $manifestContent = file_get_contents($manifestPath);
                if (empty($manifestContent) || $manifestContent === '{}') {
                    Log::warning('Vite manifest.json is empty', [
                        'content' => $manifestContent,
                    ]);
                    
                    // Create a proper manifest
                    $this->createFallbackManifest();
                } else {
                    try {
                        json_decode($manifestContent, true, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $e) {
                        Log::warning('Vite manifest.json is not valid JSON', [
                            'error' => $e->getMessage(),
                        ]);
                        
                        // Create a proper manifest
                        $this->createFallbackManifest();
                    }
                }
            }
        }
        
        return $next($request);
    }
    
    /**
     * Create a fallback manifest.json file with basic entries
     */
    private function createFallbackManifest(): void
    {
        $buildDir = public_path('build');
        $assetsDir = public_path('build/assets');
        
        // Create directories if they don't exist
        if (!is_dir($buildDir)) {
            mkdir($buildDir, 0755, true);
        }
        
        if (!is_dir($assetsDir)) {
            mkdir($assetsDir, 0755, true);
        }
        
        // Create a basic manifest
        $manifest = [
            'resources/css/app.css' => [
                'file' => 'assets/app.css',
                'src' => 'resources/css/app.css',
                'isEntry' => true,
            ],
            'resources/js/app.js' => [
                'file' => 'assets/app.js',
                'src' => 'resources/js/app.js',
                'isEntry' => true,
            ],
        ];
        
        // Write the manifest file
        file_put_contents(
            public_path('build/manifest.json'),
            json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
        
        // Create basic CSS and JS files if they don't exist
        if (!file_exists(public_path('build/assets/app.css'))) {
            file_put_contents(
                public_path('build/assets/app.css'),
                "/* Fallback CSS file created by HandleViteAssets middleware */\n"
            );
        }
        
        if (!file_exists(public_path('build/assets/app.js'))) {
            file_put_contents(
                public_path('build/assets/app.js'),
                "console.log('Fallback JS file created by HandleViteAssets middleware');\n"
            );
        }
        
        // Log the action
        Log::info('Created fallback Vite manifest and assets');
    }
}
