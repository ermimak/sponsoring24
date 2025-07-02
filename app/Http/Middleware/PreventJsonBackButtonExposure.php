<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class PreventJsonBackButtonExposure
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Process the request and get the response
        $response = $next($request);
        
        // Log the request and response details for debugging
        Log::debug('PreventJsonBackButtonExposure middleware', [
            'path' => $request->path(),
            'method' => $request->method(),
            'content_type' => $response->headers->get('Content-Type'),
            'status' => $response->getStatusCode(),
        ]);
        
        // Apply strict no-cache headers to all responses
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0, max-age=0, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '-1');
        
        // Add X-Content-Type-Options to prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Add security headers
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Add Content-Security-Policy to prevent data leakage
        if (!App::environment('local', 'testing')) {
            $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:;");
        }
        
        // For JSON responses, add special handling
        if ($this->isJsonResponse($response)) {
            // Set a cookie to track that we've seen a JSON response
            if (!$request->cookie('json_response_seen')) {
                $response->headers->setCookie(cookie('json_response_seen', '1', 0, '/', null, secure: true, httpOnly: true, sameSite: 'lax'));
            }
            
            // Check if this is a back navigation to a JSON response
            if ($request->header('Sec-Fetch-Dest') === 'document' && 
                $request->header('Sec-Fetch-Mode') === 'navigate' && 
                $request->header('Sec-Fetch-Site') === 'none') {
                
                // Redirect to dashboard to prevent JSON exposure
                return redirect()->route('dashboard');
            }
        }
        
        return $response;
    }
    
    /**
     * Determine if the response is JSON.
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return bool
     */
    protected function isJsonResponse($response): bool
    {
        $contentType = $response->headers->get('Content-Type');
        
        if (!$contentType) {
            return false;
        }
        
        return (
            str_contains($contentType, 'application/json') || 
            str_contains($contentType, 'text/json') ||
            str_contains($contentType, '+json')
        );
    }
}
