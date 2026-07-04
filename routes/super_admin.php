<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CirculationController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\FundingSourceController;  // 🔥 TAMBAHKAN INI

// ==================== DASHBOARD ====================
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// ==================== ITEMS ====================
Route::resource('items', ItemController::class);
Route::get('items/{item}/pdf', [ItemController::class, 'generatePdf'])->name('items.pdf');

// ==================== CATEGORIES ====================
Route::resource('categories', CategoryController::class);

// ==================== UNITS ====================
Route::resource('units', UnitController::class);

// ==================== USERS (MANAJEMEN AKUN) ====================
Route::resource('users', UserController::class);

// ==================== FUNDING SOURCES (SUMBER DANA) ====================
Route::resource('funding-sources', FundingSourceController::class);

// ==================== CIRCULATIONS (SIRKULASI) ====================
Route::prefix('circulations')->name('circulations.')->group(function () {
    Route::get('/', [CirculationController::class, 'index'])->name('index');
    Route::get('/{circulation}', [CirculationController::class, 'show'])->name('show');
    Route::post('/{circulation}/approve', [CirculationController::class, 'approve'])->name('approve');
    Route::post('/{circulation}/reject', [CirculationController::class, 'reject'])->name('reject');
    Route::post('/{circulation}/return', [CirculationController::class, 'markReturned'])->name('return');
    Route::post('/{circulation}/confirm-return', [CirculationController::class, 'confirmReturn'])->name('confirm-return');
});

// ==================== NOTIFICATIONS ====================
Route::prefix('notifications')->name('notifications.')->group(function () {
    Route::post('/mark-read/{id}', [NotificationController::class, 'markAsRead'])->name('markRead');
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('markAllRead');
});