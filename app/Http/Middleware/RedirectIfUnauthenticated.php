<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;

class RedirectIfUnauthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (!Auth::guard($guard)->check()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Unauthenticated',
                        'detail' => 'You must be logged in to access this resource.'
                    ], 401);
                }

                // Store the intended URL to redirect back after login
                if (!$request->is('login', 'register', 'password/*', 'email/*')) {
                    session()->put('url.intended', url()->current());
                }

                // Redirect with a friendly message
                return redirect()->route('login')->with('error', 'Please log in to access this page.');
            }
        }

        return $next($request);
    }
}
