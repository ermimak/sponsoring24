<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguageController;

Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::get('/projects', function () {
    return Inertia::render('Projects/Index');
})->name('projects.index');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/register', function () {
    return Inertia::render('Auth/Register');
})->name('register')->middleware('guest');

// Dashboard routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard/Index');
    })->name('dashboard');

    Route::get('/dashboard/projects', function () {
        return Inertia::render('Dashboard/Projects/Index');
    })->name('dashboard.projects');

    Route::get('/dashboard/donations', function () {
        return Inertia::render('Dashboard/Donations/Index');
    })->name('dashboard.donations');

    Route::get('/dashboard/reports', function () {
        return Inertia::render('Dashboard/Reports/Index');
    })->name('dashboard.reports');

    // Admin
    Route::get('/dashboard/admin/roles', function () {
        return Inertia::render('Dashboard/Roles');
    })->name('dashboard.admin.roles');
    Route::get('/dashboard/admin/permissions', function () {
        return Inertia::render('Dashboard/Permissions');
    })->name('dashboard.admin.permissions');
});

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

Route::get('language/{locale}', [App\Http\Controllers\LanguageController::class, 'switch'])
    ->name('language.switch')
    ->where('locale', 'de|fr');
