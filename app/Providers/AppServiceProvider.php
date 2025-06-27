<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
            URL::forceScheme('https');
        }
        
        // Share Stripe public key with all views
        $stripeKey = config('services.stripe.key');
        
        // Share with Inertia
        \Inertia\Inertia::share('stripeKey', $stripeKey);
    }
}
