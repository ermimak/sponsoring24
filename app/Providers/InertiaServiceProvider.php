<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class InertiaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Inertia::setRootView('app');

        Inertia::share([
            'auth' => function () {
                $user = Auth::user();

                Log::info('Inertia share user data', [
                    'user_object' => $user ? $user->toArray() : null,
                    'session_all' => session()->all(),
                ]);

                return [
                    'user' => $user ? [
                        'id' => $user->id,
                        'name' => $user->name ?? $user->email ?? 'User',
                        'email' => $user->email ?? null,
                        'organization' => $user->organization ?? 'Org',
                        'permissions' => $user->permissions, // Use the accessor
                        'roles' => $user->getRoleNames()->toArray(),
                    ] : null,
                ];
            },
            'locale' => function () {
                return app()->getLocale();
            },
            'flash' => function () {
                return [
                    'message' => session()->get('message'),
                    'error' => session()->get('error'),
                    'success' => session()->get('success'),
                ];
            },
            'errors' => function () {
                return session()->get('errors') ? session()->get('errors')->getBag('default')->toArray() : (object) [];
            },
        ]);
    }
}
