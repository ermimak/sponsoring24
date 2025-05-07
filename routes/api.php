<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ParticipantController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('projects', ProjectController::class);
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