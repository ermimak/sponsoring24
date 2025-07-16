<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ValidateUuidSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $userId = Auth::id();
            
            // Check if the user ID is a valid UUID
            if (!Str::isUuid($userId)) {
                // If not a valid UUID, log the user out
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Redirect to login page
                return redirect()->route('login')->with('message', 'Please log in again to continue.');
            }
        }

        return $next($request);
    }
}
