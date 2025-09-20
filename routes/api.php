<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ParticipantController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::middleware('auth:sanctum')->group(function () {
    // Route::apiResource('projects', ProjectController::class)->where(['project' => '[0-9a-fA-F-]{36}']);
    Route::get('/projects', [ProjectController::class, 'index'])->name('api.projects');

    // Add route to fetch a specific project by ID
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('api.projects.show');
    Route::apiResource('participants', ParticipantController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('permissions', PermissionController::class);
    Route::post('roles/{role}/assign-permission', [RoleController::class, 'assignPermission']);
    Route::post('roles/{role}/remove-permission', [RoleController::class, 'removePermission']);
    Route::post('roles/{role}/assign-user', [RoleController::class, 'assignUser']);
    Route::post('roles/{role}/remove-user', [RoleController::class, 'removeUser']);
    Route::post('permissions/{permission}/assign-role', [PermissionController::class, 'assignRole']);
    Route::post('permissions/{permission}/remove-role', [PermissionController::class, 'removeRole']);
    // Add more resources as needed (donations, groups, etc.)
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

// Move upload route inside auth middleware group since it requires authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/upload', function (Request $request) {
        $request->validate([
            'image_landscape' => 'nullable|image|max:6144',
            'image_square' => 'nullable|image|max:6144',
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
    });

});

// Route::get('/projects', [ProjectController::class, 'index']);
