<?php

use App\Http\Controllers\AdminUnit\DashboardController;
use App\Http\Controllers\AdminUnit\ItemController;
use App\Http\Controllers\AdminUnit\CirculationController;
use App\Http\Controllers\Admin\NotificationController;

// ==================== DASHBOARD ====================
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// ==================== ITEMS ====================
Route::prefix('items')->group(function () {
    Route::get('/', [ItemController::class, 'index'])->name('items.index');
    Route::get('/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/', [ItemController::class, 'store'])->name('items.store');
    Route::get('/{item}', [ItemController::class, 'show'])->name('items.show');
    
    // 🔥 ROUTE PDF
    Route::get('/{item}/pdf', [ItemController::class, 'generatePdf'])->name('items.pdf');
    
    // 🔥 ROUTE PNG (TAMBAHKAN INI)
    Route::get('/{item}/png', [ItemController::class, 'generatePng'])->name('items.png');
});

// ==================== CIRCULATIONS ====================
Route::prefix('circulations')->group(function () {
    Route::get('/', [CirculationController::class, 'index'])->name('circulations.index');
    Route::get('/{circulation}', [CirculationController::class, 'show'])->name('circulations.show');
    Route::post('/{circulation}/approve', [CirculationController::class, 'approve'])->name('circulations.approve');
    Route::post('/{circulation}/reject', [CirculationController::class, 'reject'])->name('circulations.reject');
    Route::post('/{circulation}/return', [CirculationController::class, 'markReturned'])->name('circulations.return');
    Route::post('/{circulation}/confirm-return', [CirculationController::class, 'confirmReturn'])->name('circulations.confirm-return');
});

// ==================== NOTIFICATIONS ====================
Route::prefix('notifications')->name('notifications.')->group(function () {
    Route::post('/mark-read/{id}', [NotificationController::class, 'markAsRead'])->name('markRead');
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('markAllRead');
});