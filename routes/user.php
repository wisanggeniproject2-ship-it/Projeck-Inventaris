<?php

use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ItemController;
use App\Http\Controllers\User\CirculationController;
use App\Http\Controllers\User\AssetDisposalController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// ============================================================
// ITEMS
// ============================================================
Route::get('items', [ItemController::class, 'index'])->name('items.index');

// 🔥 Ambil daftar unit yang punya barang ini (untuk modal pilih unit)
// PENTING: route ini HARUS di atas 'items/{item}' biar tidak bentrok
Route::get('items/{item}/units', [ItemController::class, 'getUnitsForItem'])->name('items.units-for-item');

// 🔥🔥🔥 BARU: Ambil daftar kode stok yang available (untuk form disposal)
// PENTING: route ini HARUS di atas 'items/{item}' biar tidak bentrok
Route::get('items/{item}/stock-codes', [AssetDisposalController::class, 'getStockCodes'])
    ->name('items.stock-codes');

Route::get('items/{item}', [ItemController::class, 'show'])->name('items.show');

// ============================================================
// CIRCULATIONS
// ============================================================
Route::get('circulations', [CirculationController::class, 'index'])->name('circulations.index');
Route::get('circulations/create', [CirculationController::class, 'create'])->name('circulations.create');
Route::post('circulations', [CirculationController::class, 'store'])->name('circulations.store');
Route::put('circulations/{circulation}/return', [CirculationController::class, 'returnItem'])->name('circulations.return');
Route::put('circulations/{circulation}/request-return', [CirculationController::class, 'requestReturn'])->name('circulations.requestReturn');

// ============================================================
// 🔥 PENGAJUAN ASET (PENGHAPUSAN BARANG RUSAK)
// ⚠️ URUTAN PENTING: 'create' HARUS sebelum route lain yang statis, aman di sini
// ============================================================
Route::get('disposals', [AssetDisposalController::class, 'index'])->name('disposals.index');
Route::get('disposals/create', [AssetDisposalController::class, 'create'])->name('disposals.create');
Route::post('disposals', [AssetDisposalController::class, 'store'])->name('disposals.store');