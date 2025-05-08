<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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

        $user = User::where('email', $request->email)->first();
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            if ($request->wantsJson()) {
                throw ValidationException::withMessages([
                    'email' => [trans('auth.failed')],
                ]);
            }
            return back()->withErrors([
                'email' => trans('auth.failed'),
            ]);
        }

        // Log in the user
        Auth::login($user, $request->boolean('remember'));
        
        // Regenerate session
        $request->session()->regenerate();
        
        // Debug logging
        Log::info('User logged in', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'permissions' => $user->permissions->pluck('name')->toArray(),
                'roles' => $user->getRoleNames()->toArray(),
            ],
            'session' => $request->session()->all(),
            'auth' => Auth::check()
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'permissions' => $user->permissions->pluck('name')->toArray(),
                    'roles' => $user->getRoleNames()->toArray(),
                ]
            ]);
        }

        // Use Inertia redirect instead of location
        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Logged out']);
        }

        return Inertia::location(route('login'));
    }

    public function showRegister()
    {
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assign default role if needed
        // $user->assignRole('user');

        Auth::login($user);
        $request->session()->regenerate();

        return Inertia::location(route('dashboard'));
    }
} 