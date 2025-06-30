<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->hasRole('super-admin')) {
            // Log unauthorized access attempt
            \Illuminate\Support\Facades\Log::warning('Unauthorized admin access attempt', [
                'user_id' => $request->user() ? $request->user()->id : null,
                'email' => $request->user() ? $request->user()->email : null,
                'ip' => $request->ip(),
                'url' => $request->fullUrl()
            ]);
            
            // redirect back to dashboard
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
