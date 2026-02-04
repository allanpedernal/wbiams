<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\IpAddressController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // IP Address Management Routes
    Route::resource('ip-addresses', IpAddressController::class);

    // Audit Log Routes (Super Admin Only)
    Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
        Route::get('/user/{userId}', [AuditLogController::class, 'userActivities'])->name('user');
        Route::get('/ip-address/{ipAddressId}', [AuditLogController::class, 'ipAddressHistory'])->name('ip-address');
        Route::get('/session/{sessionId}', [AuditLogController::class, 'sessionActivities'])->name('session');
        Route::get('/{activity}', [AuditLogController::class, 'show'])->name('show');
    });
});

require __DIR__.'/settings.php';
