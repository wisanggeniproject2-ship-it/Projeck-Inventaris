<?php

use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ItemController;
use App\Http\Controllers\User\CirculationController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('items', [ItemController::class, 'index'])->name('items.index');
Route::get('items/{item}', [ItemController::class, 'show'])->name('items.show');

Route::get('circulations', [CirculationController::class, 'index'])->name('circulations.index');
Route::get('circulations/create', [CirculationController::class, 'create'])->name('circulations.create');
Route::post('circulations', [CirculationController::class, 'store'])->name('circulations.store');
Route::put('circulations/{circulation}/return', [CirculationController::class, 'returnItem'])->name('circulations.return');