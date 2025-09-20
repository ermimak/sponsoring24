<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class SecureFileUpload
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasFile('image_landscape') || $request->hasFile('image_portrait') || $request->hasFile('file')) {
            // Log file upload attempt
            Log::info('File upload attempt', [
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'files' => array_keys($request->allFiles()),
                'url' => $request->fullUrl()
            ]);

            // Scan uploaded files
            foreach ($request->allFiles() as $key => $file) {
                if (is_array($file)) {
                    foreach ($file as $singleFile) {
                        if (!$this->scanFile($singleFile, $key)) {
                            return response()->json(['error' => 'File failed security scan'], 400);
                        }
                    }
                } else {
                    if (!$this->scanFile($file, $key)) {
                        return response()->json(['error' => 'File failed security scan'], 400);
                    }
                }
            }
        }

        return $next($request);
    }

    /**
     * Scan individual file for security threats
     */
    private function scanFile($file, $fieldName): bool
    {
        // Check file size (additional layer beyond validation)
        if ($file->getSize() > 128 * 1024 * 1024) { // 128MB absolute max to match server config
            Log::warning('File too large', [
                'field' => $fieldName,
                'size' => $file->getSize(),
                'filename' => $file->getClientOriginalName()
            ]);
            return false;
        }

        // Check for double extensions (e.g., file.jpg.php)
        $filename = $file->getClientOriginalName();
        if (substr_count($filename, '.') > 1) {
            Log::warning('Suspicious filename with multiple extensions', [
                'field' => $fieldName,
                'filename' => $filename
            ]);
            return false;
        }

        // Check for null bytes in filename
        if (strpos($filename, "\0") !== false) {
            Log::warning('Null byte in filename', [
                'field' => $fieldName,
                'filename' => $filename
            ]);
            return false;
        }

        // Basic virus signature check (simple patterns)
        if ($this->hasVirusSignature($file)) {
            Log::error('Potential virus detected', [
                'field' => $fieldName,
                'filename' => $filename,
                'user_id' => auth()->id(),
                'ip' => request()->ip()
            ]);
            return false;
        }

        return true;
    }

    /**
     * Simple virus signature detection
     */
    private function hasVirusSignature($file): bool
    {
        // Only scan small files to avoid performance issues
        if ($file->getSize() > 1024 * 1024) { // 1MB
            return false;
        }

        $content = file_get_contents($file->getPathname());
        
        // Common virus signatures (simplified)
        $signatures = [
            'X5O!P%@AP[4\PZX54(P^)7CC)7}$EICAR-STANDARD-ANTIVIRUS-TEST-FILE!$H+H*', // EICAR test
            'eval(base64_decode(',
            'system($_GET',
            'shell_exec(',
            'passthru(',
            '<?php system(',
            'WScript.Shell',
            'CreateObject("WScript.Shell")',
        ];

        foreach ($signatures as $signature) {
            if (strpos($content, $signature) !== false) {
                return true;
            }
        }

        return false;
    }
}
