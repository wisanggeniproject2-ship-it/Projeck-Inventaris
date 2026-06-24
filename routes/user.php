<?php

use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ItemController;
use App\Http\Controllers\User\CirculationController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Items - View & Scan
Route::get('items', [ItemController::class, 'index'])->name('items.index');
Route::get('items/{item}', [ItemController::class, 'show'])->name('items.show');

// Circulations
Route::resource('circulations', CirculationController::class)->only(['index', 'create', 'store']);