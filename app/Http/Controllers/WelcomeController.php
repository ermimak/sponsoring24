<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        $notifications = [];
        if ($request->user()) {
            $notifications = $request->user()->notifications()->latest()->take(10)->get();
        }
        

        return Inertia::render('Welcome', [
            'notifications' => $notifications,
        ]);
    }
} 