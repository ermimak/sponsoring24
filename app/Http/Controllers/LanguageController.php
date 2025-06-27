<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switch($locale)
    {
        if (! in_array($locale, ['de', 'fr'])) {
            $locale = 'de';
        }

        App::setLocale($locale);
        Session::put('locale', $locale);

        return redirect()->back();
    }
}
