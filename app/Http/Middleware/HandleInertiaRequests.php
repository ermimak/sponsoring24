<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        // Enhanced debug logging
        Log::info('Inertia share user data', [
            'user_object' => $user ? $user->toArray() : null,
            'session_auth' => $request->session()->get('auth'),
            'request_path' => $request->path(),
            'request_method' => $request->method(),
        ]);

        // Get notifications for authenticated user
        $notifications = [];
        $unreadNotificationsCount = 0;
        
        if ($user) {
            try {
                $notifications = $user->notifications()->latest()->limit(10)->get();
                $unreadNotificationsCount = $user->unreadNotifications()->count();
                
                // Force reload user to ensure we have the latest data
                $user->refresh();
                
                Log::debug('User refreshed successfully', ['user_id' => $user->id]);
            } catch (\Exception $e) {
                Log::error('Error loading user notifications', [
                    'error' => $e->getMessage(),
                    'user_id' => $user->id
                ]);
            }
        }
        
        // Prepare user data with all necessary fields
        $userData = null;
        if ($user) {
            try {
                // Get all user attributes to ensure we don't miss any
                $userAttributes = $user->getAttributes();
                
                $userData = [
                    'id' => $user->id,
                    'name' => $user->name ?? $user->email ?? 'User',
                    'email' => $user->email,
                    'organization' => $user->organization ?? null,
                    'address' => $user->address ?? null,
                    'address_suffix' => $user->address_suffix ?? null,
                    'postal_code' => $user->postal_code ?? null,
                    'city' => $user->city ?? null,
                    'location' => $user->location ?? null,
                    'country' => $user->country ?? null,
                    'phone' => $user->phone ?? null,
                    'language' => $user->language ?? app()->getLocale(),
                    'permissions' => method_exists($user, 'permissions') ? $user->permissions->pluck('name')->toArray() : [],
                    'roles' => method_exists($user, 'getRoleNames') ? $user->getRoleNames()->toArray() : [],
                    'created_at' => $user->created_at ? $user->created_at->toIso8601String() : null,
                    'updated_at' => $user->updated_at ? $user->updated_at->toIso8601String() : null,
                ];
                
                // Include any additional fields from the user model that we might have missed
                foreach ($userAttributes as $key => $value) {
                    if (!array_key_exists($key, $userData) && !in_array($key, ['password', 'remember_token'])) {
                        $userData[$key] = $value;
                    }
                }
                
                Log::debug('User data prepared for Inertia', ['user_id' => $user->id, 'fields' => array_keys($userData)]);
            } catch (\Exception $e) {
                Log::error('Error preparing user data for Inertia', [
                    'error' => $e->getMessage(),
                    'user_id' => $user->id
                ]);
                
                // Fallback to basic user data
                $userData = [
                    'id' => $user->id,
                    'name' => $user->name ?? $user->email ?? 'User',
                    'email' => $user->email,
                ];
            }
        }
        
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $userData,
                'check' => !!$user,
            ],
            'locale' => app()->getLocale(),
            'notifications' => $notifications,
            'unreadNotificationsCount' => $unreadNotificationsCount,
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
                'success' => fn () => $request->session()->get('success'),
            ],
            'errors' => fn () => $request->session()->get('errors') ? $request->session()->get('errors')->getBag('default')->toArray() : (object) [],
            'appUrl' => config('app.url'),
            'appName' => config('app.name'),
            'appEnv' => config('app.env'),
        ]);
    }
}
