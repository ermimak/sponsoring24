<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = Session::get('locale', 'de');
        
        if (! in_array($locale, ['de', 'fr'])) {
            $locale = 'de';
        }

        App::setLocale($locale);
        Session::put('locale', $locale);

        return $next($request);
    }
}
