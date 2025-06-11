<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BonusCreditController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\MemberGroupController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\Admin\SuperAdminController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\UserActivityController;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

// Public Routes
Route::get('/', [WelcomeController::class, 'index'])->name('home');
Route::get('/projects', fn () => Inertia::render('Projects/Index'))->name('projects.index');

// Test routes (no auth required)
Route::get('/test-email/{type?}', [ParticipantController::class, 'testTemplateEmail'])->name('test-template-email');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/register-with-referral', [AuthController::class, 'register'])->name('register.with_referral');
});

// Language Routes
Route::get('language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch')
    ->where(['locale' => 'de|fr']);

// Localized Welcome Pages
Route::get('/{locale}', fn ($locale) => Inertia::render('Welcome', ['locale' => $locale]))
    ->where(['locale' => 'de|fr'])
    ->name('welcome.locale');

// Protected Routes
Route::middleware(['auth', 'web'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard Routes
    Route::get('/dashboard', fn () => Inertia::render('Dashboard/Index'))->name('dashboard');

    // Projects
    Route::prefix('dashboard/projects')->name('dashboard.projects.')->group(function () {
        Route::get('/', function (Request $request) {
            $projects = (new ProjectController())->index($request);

            return Inertia::render('Projects/Index', [
                'projects' => $projects,
            ]);
        })->name('index');
        Route::get('/create', fn () => Inertia::render('Projects/Create'))->name('create');
        Route::get('/{project}/edit', [ProjectController::class, 'show'])
            ->name('edit')
            ->where(['project' => '[0-9a-fA-F-]{36}']);
        Route::put('/{project}/update', [ProjectController::class, 'update'])
            ->name('update')
            ->where(['project' => '[0-9a-fA-F-]{36}']);
        Route::post('/{project}/upload-image', [ProjectController::class, 'uploadImage'])
            ->name('uploadImage')
            ->where(['project' => '[0-9a-fA-F-]{36}']);
        Route::post('/{project}/duplicate', [ProjectController::class, 'duplicate'])
            ->name('duplicate')
            ->where(['project' => '[0-9a-fA-F-]{36}']);
    });
    Route::apiResource('dashboard/projects', ProjectController::class)
        ->except(['index', 'create', 'edit', 'show', 'uploadImage', 'duplicate','update'])
        ->where(['project' => '[0-9a-fA-F-]{36}']);

    // Donation
    Route::prefix('projects/{projectId}')->group(function () {
        Route::get('donations', [DonationController::class, 'index'])->name('project.donations.index');
        Route::post('donations/mass-email', [DonationController::class, 'sendMassEmail'])->name('project.donations.massEmail');
        Route::post('donations/bulk-invoice', [DonationController::class, 'bulkInvoice'])->name('project.donations.bulkInvoice');
        Route::post('donations/{donationId}/send-email', [DonationController::class, 'sendEmail'])->name('project.donations.sendEmail');
    });
    Route::get('/dashboard/projects/{project}/donations', [App\Http\Controllers\DonationController::class, 'fetchDonations'])
    ->name('dashboard.project.donations.fetch');

    // Members
    Route::get('/dashboard/members', [ParticipantController::class, 'indexAll'])->name('dashboard.members.index');
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
        if (! $participant->exists) {
            abort(404, 'Participant not found');
        }

        return Inertia::render('Members/Create', [
            'routeParams' => ['id' => $participant->id],
        ]);
    })->name('dashboard.members.edit')->where('participant', '[0-9]+');
    Route::post('/dashboard/members/import', [ParticipantController::class, 'import'])->name('dashboard.members.import');
    Route::get('/dashboard/members/export', [ParticipantController::class, 'export'])->name('dashboard.members.export');
    Route::get('/dashboard/members/test-email', [ParticipantController::class, 'testEmail'])->name('members.test-email');
    

    // Group routes
    Route::get('/dashboard/members/groups', [MemberGroupController::class, 'index'])->name('dashboard.members.groups');
    Route::get('/dashboard/members/groups/data', [MemberGroupController::class, 'data'])->name('dashboard.members.groups.data');
    Route::post('/dashboard/members/groups', [MemberGroupController::class, 'store'])->name('dashboard.members.groups.store');
    Route::delete('/dashboard/members/groups/{memberGroup}', [MemberGroupController::class, 'destroy'])->name('dashboard.members.groups.destroy')->where('memberGroup', '[0-9]+');

    // Notification Routes
    Route::get('/dashboard/notifications', [NotificationController::class, 'index'])->name('dashboard.notifications.index');
    Route::post('/dashboard/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('dashboard.notifications.mark-as-read');
    Route::post('/dashboard/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('dashboard.notifications.mark-all-as-read');
    
    // Other Dashboard Routes
    Route::get('/dashboard/settings', [SettingsController::class, 'index'])->name('dashboard.settings');
    Route::post('/dashboard/settings', [SettingsController::class, 'update'])->name('dashboard.settings.update');

    Route::get('/dashboard/users', [UserController::class, 'index'])->name('dashboard.users');
    Route::get('/dashboard/users/create', [UserController::class, 'create'])->name('dashboard.users.create');
    Route::get('/dashboard/users/{id}/edit', [UserController::class, 'edit'])->name('dashboard.users.edit');
    Route::post('/dashboard/users', [UserController::class, 'store'])->name('dashboard.users.store');
    Route::delete('/dashboard/users/{id}', [UserController::class, 'destroy'])->name('dashboard.users.destroy');

    Route::get('/dashboard/bonus', [BonusCreditController::class, 'index'])->name('dashboard.bonus.index');
    Route::post('/register-with-referral', [BonusCreditController::class, 'registerWithReferral'])->name('register.with_referral');
    Route::post('/dashboard/bonus/{bonusCredit}/credit', [BonusCreditController::class, 'creditBonus'])->name('dashboard.bonus.credit');

    // License routes
    Route::get('/license', [LicenseController::class, 'index'])->name('license.purchase');
    Route::get('/dashboard/license', [LicenseController::class, 'dashboard'])->name('dashboard.license');
    Route::post('/license/create-payment-intent', [LicenseController::class, 'createPaymentIntent'])->name('license.create-payment-intent');
    Route::post('/webhook/license/stripe', [LicenseController::class, 'handleWebhook'])->name('webhook.license.stripe');
    
    // Referrals
    Route::get('/dashboard/referrals', [ReferralController::class, 'index'])->name('dashboard.referrals');

    Route::get('/dashboard/donations', fn () => Inertia::render('Dashboard/Donations/Index'))->name('dashboard.donations');
    Route::get('/dashboard/reports', fn () => Inertia::render('Dashboard/Reports/Index'))->name('dashboard.reports');

    // Participant Routes
    Route::prefix('dashboard/projects/{projectId}')->group(function () {
        Route::get('/participants', [ParticipantController::class, 'index']);
        Route::post('/participants', [ParticipantController::class, 'addToProject']);
        Route::get('/participants/create', fn ($projectId) => Inertia::render('Projects/Participants/Create', ['projectId' => $projectId]))->name('participants.create');
        Route::post('/participants/mass-email', [ParticipantController::class, 'sendMassEmail'])->name('project.participants.massEmail');
        Route::get('/participants/export', [ParticipantController::class, 'export']); // Added for export
    });
    
    // Individual participant email route
    Route::post('dashboard/participants/{participantId}/send-email', [ParticipantController::class, 'sendEmail'])->name('dashboard.participants.sendEmail');

    Route::apiResource('dashboard/email-templates', EmailTemplateController::class)
        ->except(['create', 'edit'])
        ->where(['email_template' => '[0-9]+']);

    Route::apiResource('dashboard/member-groups', MemberGroupController::class)
        ->except(['create', 'edit'])
        ->where(['member_group' => '[0-9]+']);

    Route::middleware('can:manage_permissions')->group(function () {
        Route::get('/dashboard/admin/permissions', fn () => Inertia::render('Dashboard/Permissions'))->name('dashboard.admin.permissions');
    });
    
    // Super Admin Routes
    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        
        // User Management
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/pending', [AdminUserController::class, 'pending'])->name('users.pending');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::post('/users/{user}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
        Route::post('/users/{user}/reject', [AdminUserController::class, 'reject'])->name('users.reject');
        
        // Referral and Discount Management
        Route::get('/referrals', [\App\Http\Controllers\Admin\ReferralManagementController::class, 'index'])->name('referrals');
        Route::post('/referrals/{bonusCredit}/update-status', [\App\Http\Controllers\Admin\ReferralManagementController::class, 'updateStatus'])->name('referrals.update-status');
        Route::get('/discounts', [\App\Http\Controllers\Admin\ReferralManagementController::class, 'discounts'])->name('discounts');
        
        Route::get('/content', [\App\Http\Controllers\Admin\ContentController::class, 'index'])->name('content.index');
        Route::get('/content/featured-projects', [\App\Http\Controllers\Admin\ContentController::class, 'featuredProjects'])->name('content.featured-projects');
        Route::post('/content/featured-projects', [\App\Http\Controllers\Admin\ContentController::class, 'updateFeatured'])->name('content.featured-projects.update');
        Route::get('/dashboard/admin/roles', fn () => Inertia::render('Dashboard/Roles'))->name('dashboard.admin.roles');
        Route::get('/content/news', [\App\Http\Controllers\Admin\ContentController::class, 'news'])->name('content.news');
        Route::put('/content/news', [\App\Http\Controllers\Admin\ContentController::class, 'updateNews'])->name('content.news.update');
        Route::post('/content/news/create', [\App\Http\Controllers\Admin\ContentController::class, 'createNews'])->name('content.news.create');
        Route::delete('/content/news/{news}', [\App\Http\Controllers\Admin\ContentController::class, 'destroyNews'])->name('content.news.destroy');
        
        // Hero Section Management
        Route::get('/content/hero', [SuperAdminController::class, 'heroIndex'])->name('content.hero');
        Route::post('/content/hero', [SuperAdminController::class, 'updateHero'])->name('content.hero.update');
        
        // User Activity Tracking
        Route::get('/user-activities', [UserActivityController::class, 'index'])->name('user-activities.index');
        Route::get('/user-activities/{activity}', [UserActivityController::class, 'show'])->name('user-activities.show');
        Route::get('/users/{user}/activities', [UserActivityController::class, 'userActivities'])->name('users.activities');
        
        // Admin Notification Routes
        Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{notification}/mark-as-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
        Route::post('/notifications/mark-all-as-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
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

    Route::get('/dashboard/settings', [SettingsController::class, 'index'])->name('dashboard.settings');
    Route::post('/dashboard/settings', [SettingsController::class, 'update'])->name('dashboard.settings.update');

    // Notification routes
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.markAllAsRead');
});

// Public Routes
// Route::get('api/projects/{project}', [ProjectController::class, 'show']);
// Route::get('api/projects/{project}/participants/{participant}', [PublicParticipantController::class, 'show']);
// Route::post('api/projects/{project}/participants/{participant}/donate', [PublicParticipantController::class, 'donate']);
Route::prefix('projects/{projectId}')->group(function () {
    Route::get('participants/{participantId}', [ParticipantController::class, 'showLandingPage'])->name('participant.landing');
    Route::get('participants/{participantId}/donate', [ParticipantController::class, 'showDonationPage'])->name('participant.donate');
    Route::post('participants/{participantId}/donate', [ParticipantController::class, 'storeDonation'])->name('participant.donate.store');
});
Route::get('projects/{projectId}/participants/{participantId}/donate/confirm/{token}', [ParticipantController::class, 'confirmDonation'])->name('participant.donate.confirm');
Route::get('projects/{projectId}/participants/{participantId}/donate/payment/{donationId}', [ParticipantController::class, 'showPaymentOptions'])->name('participant.donate.payment');

Route::get('donations/{donation}/preview', [DonationController::class, 'showPreview'])->name('donations.preview');


// News show
Route::get('/content/news/{news}', [\App\Http\Controllers\Admin\ContentController::class, 'showNews'])->name('content.news.show');

// Inertia Routes for Public Pages
// Route::get('projects/{project}/participants/{participant}/donate', function ($project, $participant) {
//     return Inertia::render('Projects/ParticipantDonation', ['projectId' => $project, 'participantId' => $participant]);
// })->name('participant.donate');

// API Routes
Route::get('/api/projects', [ProjectController::class, 'index'])->name('api.projects');

// Payment Routes
Route::post('/api/create-payment-intent', [PaymentController::class, 'createPaymentIntent'])->name('payment.intent');
Route::post('/api/webhook/stripe', [PaymentController::class, 'handleWebhook'])->name('payment.webhook');

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


