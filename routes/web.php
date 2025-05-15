<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\MemberGroupController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\EmailTemplateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Participant;

// Public Routes
Route::get('/', fn() => Inertia::render('Welcome'))->name('home');
Route::get('/projects', fn() => Inertia::render('Projects/Index'))->name('projects.index');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Language Routes
Route::get('language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch')
    ->where(['locale' => 'de|fr']);

// Localized Welcome Pages
Route::get('/{locale}', fn($locale) => Inertia::render('Welcome', ['locale' => $locale]))
    ->where(['locale' => 'de|fr'])
    ->name('welcome.locale');

// Protected Routes
Route::middleware(['auth', 'web'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard Routes
    Route::get('/dashboard', fn() => Inertia::render('Dashboard/Index'))->name('dashboard');

    // Projects
    Route::prefix('dashboard/projects')->name('dashboard.projects.')->group(function () {
        Route::get('/', function (Request $request) {
            $projects = (new ProjectController())->index($request);
            return Inertia::render('Projects/Index', [
                'projects' => $projects,
            ]);
        })->name('index');
        Route::get('/create', fn() => Inertia::render('Projects/Create'))->name('create');
        Route::get('/{project}/edit', [ProjectController::class, 'show'])
            ->name('edit')
            ->where(['project' => '[0-9a-fA-F-]{36}']);
        Route::post('/{project}/upload-image', [ProjectController::class, 'uploadImage'])
            ->name('uploadImage')
            ->where(['project' => '[0-9a-fA-F-]{36}']);
        Route::post('/{project}/duplicate', [ProjectController::class, 'duplicate'])
            ->name('duplicate')
            ->where(['project' => '[0-9a-fA-F-]{36}']);
    });
    Route::apiResource('dashboard/projects', ProjectController::class)
        ->except(['index', 'create', 'edit', 'show'])
        ->where(['project' => '[0-9a-fA-F-]{36}']);

    // Members
    Route::get('/dashboard/members', [ParticipantController::class, 'index'])->name('dashboard.members.index');
    Route::post('/dashboard/members', [ParticipantController::class, 'store'])->name('dashboard.members.store');
    Route::get('/dashboard/members/create', function () {
        return Inertia::render('Members/Create', [
            'routeParams' => [],
        ]);
    })->name('dashboard.members.create');
    Route::get('/dashboard/members/{participant}', [ParticipantController::class, 'show'])->name('dashboard.members.show')->where('participant', '[0-9]+');
    Route::put('/dashboard/members/{participant}', [ParticipantController::class, 'update'])->name('dashboard.members.update')->where('participant', '[0-9]+');
    Route::delete('/dashboard/members/{participant}', [ParticipantController::class, 'destroy'])->name('dashboard.members.destroy')->where('participant', '[0-9]+');
    Route::get('/dashboard/members/{participant}/edit', function (Participant $participant) {
        if (!$participant->exists) {
            abort(404, 'Participant not found');
        }
        return Inertia::render('Members/Create', [
            'routeParams' => ['id' => $participant->id],
        ]);
    })->name('dashboard.members.edit')->where('participant', '[0-9]+');
    Route::post('/dashboard/members/import', [ParticipantController::class, 'import'])->name('dashboard.members.import');
    Route::get('/dashboard/members/export', [ParticipantController::class, 'export'])->name('dashboard.members.export');

    // Group routes
    Route::get('/dashboard/members/groups', [MemberGroupController::class, 'index'])->name('dashboard.members.groups');
    Route::get('/dashboard/members/groups/data', [MemberGroupController::class, 'data'])->name('dashboard.members.groups.data');
    Route::post('/dashboard/members/groups', [MemberGroupController::class, 'store'])->name('dashboard.members.groups.store');
    Route::delete('/dashboard/members/groups/{memberGroup}', [MemberGroupController::class, 'destroy'])->name('dashboard.members.groups.destroy')->where('memberGroup', '[0-9]+');

    // Other Dashboard Routes
    Route::get('/dashboard/users', fn() => Inertia::render('Users/Index'))->name('dashboard.users');
    Route::get('/dashboard/settings', fn() => Inertia::render('Settings/Index'))->name('dashboard.settings');
    Route::get('/dashboard/bonus', fn() => Inertia::render('Bonus/Index'))->name('dashboard.bonus');
    Route::get('/dashboard/donations', fn() => Inertia::render('Dashboard/Donations/Index'))->name('dashboard.donations');
    Route::get('/dashboard/reports', fn() => Inertia::render('Dashboard/Reports/Index'))->name('dashboard.reports');

    // Admin Routes
    Route::middleware('can:manage_roles')->group(function () {
        Route::get('/dashboard/admin/roles', fn() => Inertia::render('Dashboard/Roles'))->name('dashboard.admin.roles');
    });

    Route::middleware('can:manage_permissions')->group(function () {
        Route::get('/dashboard/admin/permissions', fn() => Inertia::render('Dashboard/Permissions'))->name('dashboard.admin.permissions');
    });

    // Resource Routes
    Route::apiResource('dashboard/donations', DonationController::class)
        ->except(['create', 'edit'])
        ->where(['donation' => '[0-9]+']);
    Route::apiResource('dashboard/email-templates', EmailTemplateController::class)
        ->except(['create', 'edit'])
        ->where(['email_template' => '[0-9]+']);

    // File Upload Route
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

// API Routes
Route::get('/api/projects', [ProjectController::class, 'index'])->name('api.projects');

// Debug Route (Remove in Production)
Route::get('/debug-login', function () {
    if (app()->environment('production')) {
        abort(404);
    }
    $user = \App\Models\User::where('email', 'test@example.com')->first();
    if ($user) {
        \Illuminate\Support\Facades\Auth::login($user);
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('debug.login');