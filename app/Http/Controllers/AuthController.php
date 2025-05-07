<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            if ($request->wantsJson()) {
                throw ValidationException::withMessages([
                    'email' => [trans('auth.failed')],
                ]);
            }
            return back()->withErrors([
                'email' => trans('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Login successful']);
        }

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Logged out']);
        }

        return redirect('/');
    }
} 