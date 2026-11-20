<?php

use App\Http\Controllers\Api\Admin\ModerationController;
use App\Http\Controllers\Api\Admin\ProjectAdminController;
use App\Http\Controllers\Api\Admin\StatsController;
use App\Http\Controllers\Api\V1\ImpressionController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\ProjectViewController;
use App\Http\Controllers\Api\V1\ReactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/health', function () {
        return response()->json(['status' => 'ok']);
    });

    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{slug}', [ProjectController::class, 'show']);
    Route::post('/projects/{slug}/view', [ProjectViewController::class, 'store'])->middleware('throttle:60,1');
    Route::post('/projects/{slug}/reactions', [ReactionController::class, 'toggle'])->middleware('throttle:30,1');

    Route::get('/impressions', [ImpressionController::class, 'index']);
    Route::post('/impressions', [ImpressionController::class, 'store'])->middleware('throttle:impressions');
});

Route::prefix('admin')->middleware('admin.token')->group(function () {
    Route::get('/stats', [StatsController::class, 'index']);

    Route::get('/projects', [ProjectAdminController::class, 'index']);
    Route::post('/projects', [ProjectAdminController::class, 'store']);
    Route::get('/projects/{project}', [ProjectAdminController::class, 'show']);
    Route::patch('/projects/{project}', [ProjectAdminController::class, 'update']);
    Route::patch('/projects/{project}/activate', [ProjectAdminController::class, 'activate']);
    Route::patch('/projects/{project}/deactivate', [ProjectAdminController::class, 'deactivate']);

    Route::get('/impressions', [ModerationController::class, 'index']);
    Route::patch('/impressions/{impression}/hide', [ModerationController::class, 'hide']);
    Route::patch('/impressions/{impression}/show', [ModerationController::class, 'show']);
    Route::delete('/impressions/{impression}', [ModerationController::class, 'destroy']);
});
