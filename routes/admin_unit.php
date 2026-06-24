<?php

use App\Http\Controllers\AdminUnit\DashboardController;
use App\Http\Controllers\AdminUnit\ItemController;
use App\Http\Controllers\AdminUnit\CirculationController;

// ==================== DASHBOARD ====================
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// ==================== ITEMS ====================
Route::prefix('items')->group(function () {
    Route::get('/', [ItemController::class, 'index'])->name('items.index');
    Route::get('/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/', [ItemController::class, 'store'])->name('items.store');
    Route::get('/{item}', [ItemController::class, 'show'])->name('items.show');
});

// ==================== CIRCULATIONS ====================
Route::prefix('circulations')->group(function () {
    Route::get('/', [CirculationController::class, 'index'])->name('circulations.index');
    Route::get('/{circulation}', [CirculationController::class, 'show'])->name('circulations.show');
    Route::post('/{circulation}/approve', [CirculationController::class, 'approve'])->name('circulations.approve');
    Route::post('/{circulation}/reject', [CirculationController::class, 'reject'])->name('circulations.reject');
    Route::post('/{circulation}/return', [CirculationController::class, 'markReturned'])->name('circulations.return');
});