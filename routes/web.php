<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\LandingPageController;
use Illuminate\Support\Facades\Route;

// ==================== LANDING PAGE ====================
Route::get('/', [LandingPageController::class, 'index'])->name('landing');

// ==================== AUTH ====================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ==================== ADMIN ====================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(base_path('routes/admin.php'));

// ==================== MANAGER ====================
Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(base_path('routes/manager.php'));

// ==================== USER ====================
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(base_path('routes/user.php'));