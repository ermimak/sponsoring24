<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            // In production, never use the Vite dev server even if a hot file accidentally exists
            Vite::useHotFile(storage_path('framework/vite.hot'));

            // Only force HTTPS when explicitly enabled (helps with Plesk Site Preview over HTTP)
            if (filter_var(env('FORCE_HTTPS', false), FILTER_VALIDATE_BOOLEAN)) {
                URL::forceScheme('https');
            }
        }
        
        // Share Stripe public key with all views
        $stripeKey = config('services.stripe.key');
        
        // Share with Inertia
        \Inertia\Inertia::share('stripeKey', $stripeKey);
    }
}
