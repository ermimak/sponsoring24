<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Inertia\Inertia;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
        
        // Handle generic exceptions
        $this->renderable(function (\Exception $e, $request) {
            if (!$e instanceof HttpException && 
                !$e instanceof AuthenticationException && 
                !$e instanceof AuthorizationException && 
                !$e instanceof TokenMismatchException && 
                !$e instanceof ValidationException && 
                !$e instanceof ModelNotFoundException) {
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'An unexpected error occurred. Please try again later.',
                    ], 500);
                }
                
                return Inertia::render('Errors/ServerError', [
                    'status' => 500,
                    'message' => app()->environment('production') 
                        ? 'An unexpected error occurred. Please try again later.' 
                        : $e->getMessage() ?: 'An unexpected error occurred',
                ])->toResponse($request)->setStatusCode(500);
            }
            
            return null;
        });

        // Handle 404 errors
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Resource not found',
                ], 404);
            }

            return Inertia::render('Errors/NotFound', [
                'status' => 404,
                'message' => 'The page you are looking for could not be found.',
            ])->toResponse($request)->setStatusCode(404);
        });

        // Handle 405 Method Not Allowed errors
        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Method not allowed',
                ], 405);
            }

            return Inertia::render('Errors/Error', [
                'status' => 405,
                'message' => 'The method used is not allowed for this resource.',
            ])->toResponse($request)->setStatusCode(405);
        });

        // Handle Authentication errors
        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated',
                ], 401);
            }

            return redirect()->guest(route('login'))->with('error', 'You must be logged in to access this resource.');
        });

        // Handle Authorization errors
        $this->renderable(function (AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 403);
            }

            return Inertia::render('Errors/Forbidden', [
                'status' => 403,
                'message' => 'You do not have permission to access this resource.',
            ])->toResponse($request)->setStatusCode(403);
        });

        // Handle CSRF token mismatch
        $this->renderable(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'CSRF token mismatch',
                ], 419);
            }

            return Inertia::render('Errors/SessionExpired', [
                'status' => 419,
                'message' => 'Your session has expired. Please refresh and try again.',
            ])->toResponse($request)->setStatusCode(419);
        });

        // Handle Model Not Found
        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Resource not found',
                ], 404);
            }

            return Inertia::render('Errors/NotFound', [
                'status' => 404,
                'message' => 'The requested resource could not be found.',
            ])->toResponse($request)->setStatusCode(404);
        });

        // Handle other HTTP exceptions
        $this->renderable(function (HttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'An error occurred',
                ], $e->getStatusCode());
            }

            $statusCode = $e->getStatusCode();
            $message = $e->getMessage();

            if (empty($message)) {
                switch ($statusCode) {
                    case 401:
                        $message = 'Unauthorized access';
                        break;
                    case 403:
                        $message = 'Forbidden access';
                        break;
                    case 404:
                        $message = 'Resource not found';
                        break;
                    case 419:
                        $message = 'Page expired';
                        break;
                    case 429:
                        $message = 'Too many requests';
                        break;
                    case 500:
                        $message = 'Server error';
                        break;
                    case 503:
                        $message = 'Service unavailable';
                        break;
                    default:
                        $message = 'An error occurred';
                }
            }

            // Use specific error pages for common status codes
            if ($statusCode === 500) {
                return Inertia::render('Errors/ServerError', [
                    'status' => $statusCode,
                    'message' => $message,
                ])->toResponse($request)->setStatusCode($statusCode);
            } else if ($statusCode === 503) {
                return Inertia::render('Errors/Maintenance', [
                    'status' => $statusCode,
                    'message' => $message ?: 'We are currently performing scheduled maintenance. Please check back soon.',
                ])->toResponse($request)->setStatusCode($statusCode);
            } else if ($statusCode === 429) {
                return Inertia::render('Errors/TooManyRequests', [
                    'status' => $statusCode,
                    'message' => $message ?: 'You have made too many requests recently. Please wait a moment before trying again.',
                ])->toResponse($request)->setStatusCode($statusCode);
            }

            return Inertia::render('Errors/Error', [
                'status' => $statusCode,
                'message' => $message,
            ])->toResponse($request)->setStatusCode($statusCode);
        });
    }
}
