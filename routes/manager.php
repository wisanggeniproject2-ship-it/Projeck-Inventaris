<?php

use App\Http\Controllers\Manager\DashboardController;
use App\Http\Controllers\Manager\ItemController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Items - View All
Route::get('items', [ItemController::class, 'index'])->name('items.index');
Route::get('items/{item}', [ItemController::class, 'show'])->name('items.show');