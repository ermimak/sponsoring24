<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\MemberGroupController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\EmailTemplateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    Route::get('/dashboard/members/create', function () {
        return Inertia::render('Members/Create');
    })->name('dashboard.members.create');

    Route::get('/dashboard/members/groups', function () {
        return Inertia::render('Members/Groups');
    })->name('dashboard.members.groups');

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

    Route::get('/dashboard/projects/create', function () {
        return Inertia::render('Projects/Create');
    })->name('dashboard.projects.create');

    Route::get('/dashboard/projects/{project}/edit', function ($project) {
        return Inertia::render('Projects/Edit', ['projectId' => $project]);
    })->name('dashboard.projects.edit');

    Route::resource('/dashboard/projects', ProjectController::class)
        ->except(['index', 'create', 'edit', 'show'])
        ->where(['project' => '[0-9a-fA-F-]{36}']);

    Route::get('/dashboard/members/create', function () {
        return Inertia::render('Members/Create');
    })->name('dashboard.members.create');

    Route::get('/dashboard/members/groups', function () {
        return Inertia::render('Members/Groups');
    })->name('dashboard.members.groups');

    Route::resource('/dashboard/members', ParticipantController::class)
        ->except(['index', 'create', 'edit'])
        ->where(['member' => '[0-9]+']);

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

    Route::resource('/dashboard/members/groups', MemberGroupController::class)->except(['create', 'edit', 'show']);
    Route::resource('/dashboard/donations', DonationController::class)->except(['create', 'edit']);
    Route::resource('/dashboard/email-templates', EmailTemplateController::class)->except(['create', 'edit']);
    Route::post('/dashboard/members/import', [ParticipantController::class, 'import'])->name('dashboard.members.import');
    Route::get('/dashboard/members/export', [ParticipantController::class, 'export'])->name('dashboard.members.export');
    Route::post('/dashboard/projects/{project}/upload-image', [ProjectController::class, 'uploadImage'])->name('dashboard.projects.uploadImage');
    
    // Add upload route
    Route::post('/upload', function (Request $request) {
        $request->validate([
            'image_landscape' => 'nullable|image|max:2048',
            'image_square' => 'nullable|image|max:2048',
        ]);

        $data = [];
        if ($request->hasFile('image_landscape')) {
            $path = $request->file('image_landscape')->store('projects/landscape', 'public');
            $data['image_landscape'] = Storage::disk('public')->url($path);
        }
        if ($request->hasFile('image_square')) {
            $path = $request->file('image_square')->store('projects/square', 'public');
            $data['image_square'] = Storage::disk('public')->url($path);
        }

        return response()->json($data);
    })->name('upload');
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

// Add this route at the end of the file (outside the middleware group)
Route::get('/api/projects', [App\Http\Controllers\ProjectController::class, 'index']);
