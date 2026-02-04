<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\IpAddressController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| JWT-like token authentication using Laravel Sanctum.
| All authenticated routes require Bearer token in Authorization header.
|
*/

// Public routes (no authentication required)
Route::prefix('auth')->name('api.auth.')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {

    // Authentication routes
    Route::prefix('auth')->name('api.auth.')->group(function () {
        Route::get('/user', [AuthController::class, 'user'])->name('user');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    });

    // IP Address Management API
    Route::apiResource('ip-addresses', IpAddressController::class)->names([
        'index' => 'api.ip-addresses.index',
        'store' => 'api.ip-addresses.store',
        'show' => 'api.ip-addresses.show',
        'update' => 'api.ip-addresses.update',
        'destroy' => 'api.ip-addresses.destroy',
    ]);
});
