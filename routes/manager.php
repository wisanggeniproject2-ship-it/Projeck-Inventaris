<?php

use App\Http\Controllers\Manager\DashboardController;
use App\Http\Controllers\Manager\ItemController;
use App\Http\Controllers\Manager\CirculationController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('items', ItemController::class)->only(['index', 'show']);
Route::get('circulations', [CirculationController::class, 'index'])->name('circulations.index');
Route::get('circulations/{circulation}', [CirculationController::class, 'show'])->name('circulations.show');