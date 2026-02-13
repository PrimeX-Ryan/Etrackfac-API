<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/departments', function() {
    return \App\Models\Department::select('id', 'name')->get();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user()->load(['roles', 'department']);
    });
    
    // Profile
    Route::put('/user/profile', [ProfileController::class, 'update']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);

    // Download
    Route::get('/submissions/{submission}/download', [SubmissionController::class, 'download']);

    // Faculty routes
    Route::middleware('role:faculty')->group(function () {
        Route::post('/submissions/upload', [SubmissionController::class, 'upload']);
        Route::get('/submissions/checklist', [SubmissionController::class, 'checklist']);
    });

    // Chair routes
    Route::middleware('role:program_chair')->group(function () {
        Route::get('/reviews', [ReviewController::class, 'index']);
        Route::post('/reviews/{submission}', [ReviewController::class, 'review']);
        Route::get('/compliance', [ReportController::class, 'compliance']);
    });

    // Dean routes
    Route::middleware('role:dean')->group(function () {
        Route::get('/reports', [ReportController::class, 'reports']);
    });

    // Admin routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', [AdminUserController::class, 'index']);
        Route::post('/admin/users', [AdminUserController::class, 'store']);
        Route::put('/admin/users/{user}', [AdminUserController::class, 'update']);
        Route::post('/admin/users/{user}/approve', [AdminUserController::class, 'approve']);
        Route::post('/admin/users/{user}/approve', [AdminUserController::class, 'approve']);
        Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy']);
        
        // Requirements management
        Route::get('/admin/requirements', [RequirementController::class, 'index']);
        Route::post('/admin/requirements', [RequirementController::class, 'store']);
        Route::put('/admin/requirements/{requirement}', [RequirementController::class, 'update']);
        Route::delete('/admin/requirements/{requirement}', [RequirementController::class, 'destroy']);
    });
});
