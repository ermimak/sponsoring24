<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response as ResponseFactory;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;

class HandleInertiaBackButton
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
        // First, check if this is a back navigation to a previously seen JSON response
        // This must be done before processing the request to intercept back navigation early
        if ($request->cookie('inertia_request') && 
            $request->header('Sec-Fetch-Dest') === 'document' && 
            $request->header('Sec-Fetch-Mode') === 'navigate') {
            
            Log::debug('Detected back navigation to JSON content', [
                'path' => $request->path(),
                'headers' => $request->headers->all(),
            ]);
            
            // Clear the cookie and redirect to dashboard
            $redirectResponse = ResponseFactory::redirectToRoute('dashboard');
            $redirectResponse->headers->clearCookie('inertia_request');
            return $redirectResponse;
        }
        
        // Process the request
        $response = $next($request);
        
        // Set strict cache control headers for all responses
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '-1');
        
        // Check if this is a JSON response (which could be an Inertia response)
        if ($this->isJsonResponse($response)) {
            // Add security headers
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'DENY');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            
            // Log JSON response for debugging
            Log::debug('JSON response detected', [
                'path' => $request->path(),
                'content_type' => $response->headers->get('Content-Type'),
            ]);
            
            // Set a cookie to track that we've seen a JSON response
            // This cookie will help us detect back navigation to JSON content
            $response->headers->setCookie(cookie(
                'inertia_request',  // name
                '1',                // value
                0,                  // minutes (0 = session cookie)
                '/',                // path
                null,               // domain
                true,               // secure
                true,               // httpOnly
                false,              // raw
                'lax'               // sameSite
            ));
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
        
        return $contentType && (
            str_contains($contentType, 'application/json') || 
            str_contains($contentType, 'text/json') ||
            str_contains($contentType, '+json')
        );
    }
}
