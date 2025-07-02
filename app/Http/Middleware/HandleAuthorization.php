<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;

class HandleAuthorization
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
        try {
            $response = $next($request);
            return $response;
        } catch (AuthorizationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Forbidden',
                    'detail' => $e->getMessage() ?: 'You do not have permission to access this resource.'
                ], 403);
            }

            return Inertia::render('Errors/Forbidden', [
                'status' => 403,
                'message' => $e->getMessage() ?: 'You do not have permission to access this resource.',
            ])->toResponse($request)->setStatusCode(403);
        }
    }
}
