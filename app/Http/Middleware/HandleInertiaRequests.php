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
            'session_all' => $request->session()->all(),
        ]);

        // Get notifications for authenticated user
        $notifications = [];
        $unreadNotificationsCount = 0;
        
        if ($user) {
            $notifications = $user->notifications()->latest()->limit(10)->get();
            $unreadNotificationsCount = $user->unreadNotifications()->count();
        }
        
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name ?? $user->email ?? 'User',
                    'email' => $user->email ?? null,
                    'organization' => $user->organization ?? 'Org',
                    'permissions' => $user->permissions->pluck('name')->toArray(), // Use Spatie's relationship
                    'roles' => $user->getRoleNames()->toArray(),
                ] : null,
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
        ]);
    }
}
