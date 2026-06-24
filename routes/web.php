<?php

use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// ==================== LANDING PAGE ====================
Route::get('/', [LandingPageController::class, 'index'])->name('landing');

// ==================== AUTH ====================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==================== SUPER ADMIN ====================
Route::middleware(['auth', 'role:super_admin'])
    ->prefix('super-admin')
    ->name('super_admin.')
    ->group(base_path('routes/super_admin.php'));

// ==================== ADMIN UNIT ====================
Route::middleware(['auth', 'role:admin_unit'])
    ->prefix('admin-unit')
    ->name('admin_unit.')
    ->group(base_path('routes/admin_unit.php'));

// ==================== MANAGER ====================
Route::middleware(['auth', 'role:manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(base_path('routes/manager.php'));

// ==================== USER ====================
Route::middleware(['auth', 'role:user'])
    ->prefix('user')
    ->name('user.')
    ->group(base_path('routes/user.php'));

    Route::get('/debug-auth', function () {
    if (!auth()->check()) {
        return ['message' => 'Not logged in'];
    }
    
    $user = auth()->user();
    return [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
        'unit' => $user->unit?->name,
    ];
});