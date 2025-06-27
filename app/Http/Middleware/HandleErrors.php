<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HandleErrors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (AuthenticationException $e) {
            // Handle authentication errors (user not logged in)
            Log::warning('Authentication error', [
                'message' => $e->getMessage(),
                'path' => $request->path(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Unauthenticated',
                    'message' => 'You must be logged in to access this resource.',
                ], 401);
            }
            
            return redirect()->guest(route('login'))
                ->with('error', 'You must be logged in to access this resource.');
        } catch (AuthorizationException $e) {
            // Handle authorization errors (user doesn't have permission)
            Log::warning('Authorization error', [
                'message' => $e->getMessage(),
                'path' => $request->path(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => 'You do not have permission to access this resource.',
                ], 403);
            }
            
            return redirect()->back()
                ->with('error', 'You do not have permission to access this resource.');
        } catch (HttpException $e) {
            // Handle HTTP exceptions (404, 500, etc.)
            Log::error('HTTP exception', [
                'message' => $e->getMessage(),
                'code' => $e->getStatusCode(),
                'path' => $request->path(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Error',
                    'message' => $e->getMessage() ?: 'An error occurred while processing your request.',
                    'code' => $e->getStatusCode(),
                ], $e->getStatusCode());
            }
            
            return redirect()->back()
                ->with('error', $e->getMessage() ?: 'An error occurred while processing your request.');
        } catch (\Exception $e) {
            // Handle all other exceptions
            Log::error('Unhandled exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'path' => $request->path(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Error',
                    'message' => 'An unexpected error occurred. Please try again later.',
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }
}
