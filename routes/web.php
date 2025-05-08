<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

// Public routes
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/projects', function () {
    return Inertia::render('Projects/Index');
})->name('projects.index');

// Auth routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Protected routes
Route::middleware(['auth', 'web'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard routes
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard/Index');
    })->name('dashboard');

    Route::get('/dashboard/projects', function () {
        return Inertia::render('Projects/Index');
    })->name('dashboard.projects');

    Route::get('/dashboard/members', function () {
        return Inertia::render('Members/Index');
    })->name('dashboard.members');

    Route::get('/dashboard/users', function () {
        return Inertia::render('Users/Index');
    })->name('dashboard.users');

    Route::get('/dashboard/settings', function () {
        return Inertia::render('Settings/Index');
    })->name('dashboard.settings');

    Route::get('/dashboard/bonus', function () {
        return Inertia::render('Bonus/Index');
    })->name('dashboard.bonus');

    Route::get('/dashboard/donations', function () {
        return Inertia::render('Dashboard/Donations/Index');
    })->name('dashboard.donations');

    Route::get('/dashboard/reports', function () {
        return Inertia::render('Dashboard/Reports/Index');
    })->name('dashboard.reports');

    // Admin routes
    Route::middleware(['can:manage_roles'])->group(function () {
        Route::get('/dashboard/admin/roles', function () {
            return Inertia::render('Dashboard/Roles');
        })->name('dashboard.admin.roles');
    });

    Route::middleware(['can:manage_permissions'])->group(function () {
        Route::get('/dashboard/admin/permissions', function () {
            return Inertia::render('Dashboard/Permissions');
        })->name('dashboard.admin.permissions');
    });
});

// Language routes
Route::get('/de', function () {
    return Inertia::render('Welcome', [
        'locale' => 'de'
    ]);
});

Route::get('/fr', function () {
    return Inertia::render('Welcome', [
        'locale' => 'fr'
    ]);
});

Route::get('language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch')
    ->where('locale', 'de|fr');

// Debug route (remove in production)
Route::get('/debug-login', function () {
    $user = User::where('email', 'test@example.com')->first();
    if ($user) {
        Auth::login($user);
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});
