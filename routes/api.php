<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\TranslationGroupController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\CacheManagementController;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/health', function() {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'memory' => memory_get_usage(),
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Language routes
    Route::apiResource('languages', LanguageController::class);
    
    // Translation group routes
    Route::apiResource('translation-groups', TranslationGroupController::class);
    
    // Translation routes
    Route::apiResource('translations', TranslationController::class);
    
    // Export routes
    Route::get('/export', [ExportController::class, 'export']);
    Route::delete('/export/cache', [ExportController::class, 'clearCache']);
    
    // Cache management
    Route::get('/cache/stats', [CacheManagementController::class, 'stats']);
    Route::delete('/cache/clear', [CacheManagementController::class, 'clear']);
});