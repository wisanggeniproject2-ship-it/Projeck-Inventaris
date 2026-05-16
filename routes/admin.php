<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CirculationController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('items', ItemController::class);
Route::resource('categories', CategoryController::class);
Route::resource('units', UnitController::class);
Route::resource('users', UserController::class);
Route::get('circulations', [CirculationController::class, 'index'])->name('circulations.index');
Route::get('circulations/{circulation}', [CirculationController::class, 'show'])->name('circulations.show');
Route::post('circulations/{circulation}/approve', [CirculationController::class, 'approve'])->name('circulations.approve');
Route::post('circulations/{circulation}/reject', [CirculationController::class, 'reject'])->name('circulations.reject');
Route::post('circulations/{circulation}/return', [CirculationController::class, 'markReturned'])->name('circulations.return');