<?php

use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ItemController;
use App\Http\Controllers\User\CirculationController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// ============================================================
// ITEMS
// ============================================================
Route::get('items', [ItemController::class, 'index'])->name('items.index');

// 🔥 BARU: Ambil daftar unit yang punya barang ini (untuk modal pilih unit)
// PENTING: route ini HARUS di atas 'items/{item}' biar tidak bentrok
Route::get('items/{item}/units', [ItemController::class, 'getUnitsForItem'])->name('items.units-for-item');

Route::get('items/{item}', [ItemController::class, 'show'])->name('items.show');

// ============================================================
// CIRCULATIONS
// ============================================================
Route::get('circulations', [CirculationController::class, 'index'])->name('circulations.index');
Route::get('circulations/create', [CirculationController::class, 'create'])->name('circulations.create');
Route::post('circulations', [CirculationController::class, 'store'])->name('circulations.store');
Route::put('circulations/{circulation}/return', [CirculationController::class, 'returnItem'])->name('circulations.return');
Route::put('circulations/{circulation}/request-return', [CirculationController::class, 'requestReturn'])->name('circulations.requestReturn');