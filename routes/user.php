<?php

use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ItemController;
use App\Http\Controllers\User\CirculationController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('items', ItemController::class)->only(['index', 'show']);
Route::resource('circulations', CirculationController::class)->only(['index', 'create', 'store']);