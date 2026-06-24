<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CirculationController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// ITEMS
Route::resource('items', ItemController::class);

// CATEGORIES
Route::resource('categories', CategoryController::class);

// UNITS
Route::resource('units', UnitController::class);

// USERS (MANAJEMEN AKUN)
Route::resource('users', UserController::class);

// CIRCULATIONS
Route::prefix('circulations')->group(function () {
    Route::get('/', [CirculationController::class, 'index'])->name('circulations.index');
    Route::get('/{circulation}', [CirculationController::class, 'show'])->name('circulations.show');
    Route::post('/{circulation}/approve', [CirculationController::class, 'approve'])->name('circulations.approve');
    Route::post('/{circulation}/reject', [CirculationController::class, 'reject'])->name('circulations.reject');
    Route::post('/{circulation}/return', [CirculationController::class, 'markReturned'])->name('circulations.return');
});