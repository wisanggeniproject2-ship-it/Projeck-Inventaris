<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Import semua Controller Utama
use App\Http\Controllers\Admin\ItemController as SuperAdminItemController;
use App\Http\Controllers\AdminUnit\ItemController as AdminUnitItemController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rute Beranda
Route::get('/', function () {
    return view('landing');
})->name('landing');

// =========================================================================
// RUTE LOGIN
// =========================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', function (Request $request) {
        $request->validate(['email' => 'required', 'password' => 'required']);

        $attempted = Auth::attempt(['email' => $request->email, 'password' => $request->password]);

        if (!$attempted && \Illuminate\Support\Facades\Schema::hasColumn('users', 'username')) {
            $attempted = Auth::attempt(['username' => $request->email, 'password' => $request->password]);
        }

        if ($attempted) {
            $request->session()->regenerate();
            $role = auth()->user()->role ?? '';

            if ($role && Route::has($role . '.dashboard')) {
                return redirect()->route($role . '.dashboard');
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['email' => 'Kredensial salah.']);
    });
});

// =========================================================================
// RUTE UMUM & PROFILE
// =========================================================================
Route::get('/dashboard', function () {
    $role = auth()->user()->role ?? '';

    if ($role && Route::has($role . '.dashboard')) {
        return redirect()->route($role . '.dashboard');
    }

    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/login')->withErrors([
        'email' => 'Role akun "' . $role . '" belum punya halaman dashboard. Hubungi admin.',
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});

// =========================================================================
// RUTE GLOBAL STIKER PNG
// =========================================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/global/items/{item}/png', function (\App\Models\Item $item) {
        if (auth()->user()->role === 'super_admin') {
            return app(SuperAdminItemController::class)->generatePng($item);
        }
        return app(AdminUnitItemController::class)->generatePng($item);
    })->name('items.png');
});

// =========================================================================
// ROUTE GROUP: SUPER ADMIN
// =========================================================================
Route::middleware(['auth', 'role:super_admin'])
    ->prefix('super-admin')
    ->name('super_admin.')
    ->group(function () {
        
        // DASHBOARD
        Route::get('/dashboard', function () {
            if (class_exists(\App\Http\Controllers\Admin\DashboardController::class)) {
                return app(\App\Http\Controllers\Admin\DashboardController::class)->index();
            }
            
            $recentCirculations = class_exists(\App\Models\Circulation::class) 
                ? \App\Models\Circulation::latest()->take(5)->get() 
                : [];
                
            return view('admin.dashboard', compact('recentCirculations'));
        })->name('dashboard');
        
        // ============================================================
        // MANAJEMEN BARANG
        // ⚠️ URUTAN PENTING: 'create' & route statis HARUS sebelum '{item}'
        // ============================================================
        Route::get('/items', [SuperAdminItemController::class, 'index'])->name('items.index');
        Route::get('/items/create', [SuperAdminItemController::class, 'create'])->name('items.create');
        Route::post('/items', [SuperAdminItemController::class, 'store'])->name('items.store');
        Route::get('/items/{item}/edit', [SuperAdminItemController::class, 'edit'])->name('items.edit');
        Route::get('/items/{item}/pdf', [SuperAdminItemController::class, 'pdf'])->name('items.pdf');
        Route::get('/items/{item}/png', [SuperAdminItemController::class, 'generatePng'])->name('items.png');
        Route::get('/items/{item}', [SuperAdminItemController::class, 'show'])->name('items.show');
        Route::put('/items/{item}', [SuperAdminItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{item}', [SuperAdminItemController::class, 'destroy'])->name('items.destroy');
        
        // ============================================================
        // MASTER DATA
        // ============================================================
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        Route::resource('units', \App\Http\Controllers\Admin\UnitController::class);
        Route::resource('funding-sources', \App\Http\Controllers\Admin\FundingSourceController::class);
        Route::resource('circulations', \App\Http\Controllers\Admin\CirculationController::class);

        // ============================================================
        // 🔥 SIRKULASI — AKSI CUSTOM
        // ============================================================
        Route::post('circulations/{circulation}/approve', 
            [\App\Http\Controllers\Admin\CirculationController::class, 'approve']
        )->name('circulations.approve');

        Route::post('circulations/{circulation}/reject', 
            [\App\Http\Controllers\Admin\CirculationController::class, 'reject']
        )->name('circulations.reject');

        Route::post('circulations/{circulation}/return', 
            [\App\Http\Controllers\Admin\CirculationController::class, 'markReturned']
        )->name('circulations.return');

        Route::post('circulations/{circulation}/confirm-return', 
            [\App\Http\Controllers\Admin\CirculationController::class, 'confirmReturn']
        )->name('circulations.confirm-return');
        
        // ============================================================
        // 🔥 FITUR NILAI ASET & PENYUSUTAN
        // ============================================================
        Route::get('assets', [\App\Http\Controllers\Admin\AssetController::class, 'index'])->name('assets.index');

        Route::get('assets/depreciation', [\App\Http\Controllers\Admin\AssetController::class, 'depreciation'])
            ->name('assets.depreciation');

        // ============================================================
        // 🔥 PENGAJUAN PENGHAPUSAN ASET
        // ============================================================
        Route::get('disposals', [\App\Http\Controllers\Admin\AssetDisposalController::class, 'index'])->name('disposals.index');
        Route::get('disposals/{disposal}', [\App\Http\Controllers\Admin\AssetDisposalController::class, 'show'])->name('disposals.show');
        Route::post('disposals/{disposal}/approve', [\App\Http\Controllers\Admin\AssetDisposalController::class, 'approve'])->name('disposals.approve');
        Route::post('disposals/{disposal}/reject', [\App\Http\Controllers\Admin\AssetDisposalController::class, 'reject'])->name('disposals.reject');
        
        // Manajemen User
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
});

// =========================================================================
// ROUTE GROUP: ADMIN UNIT
// =========================================================================
Route::middleware(['auth', 'role:admin_unit'])
    ->prefix('admin-unit')
    ->name('admin_unit.')
    ->group(function () {
        
        // DASHBOARD
        Route::get('/dashboard', function () {
            if (class_exists(\App\Http\Controllers\AdminUnit\DashboardController::class)) {
                return app(\App\Http\Controllers\AdminUnit\DashboardController::class)->index();
            }
            
            $recentCirculations = class_exists(\App\Models\Circulation::class) 
                ? \App\Models\Circulation::latest()->take(5)->get() 
                : [];
                
            return view('admin_unit.dashboard', compact('recentCirculations'));
        })->name('dashboard');
        
        // ============================================================
        // MANAJEMEN BARANG
        // ============================================================
        Route::get('/items', [AdminUnitItemController::class, 'index'])->name('items.index');
        Route::get('/items/create', [AdminUnitItemController::class, 'create'])->name('items.create');
        Route::post('/items', [AdminUnitItemController::class, 'store'])->name('items.store');
        Route::get('/items/{item}/png', [AdminUnitItemController::class, 'generatePng'])->name('items.png');
        Route::get('/items/{item}/pdf', [AdminUnitItemController::class, 'generatePdf'])->name('items.pdf');
        Route::get('/items/{item}', [AdminUnitItemController::class, 'show'])->name('items.show');
        
        // ============================================================
        // 🔥 SIRKULASI — AKSI CUSTOM
        // ============================================================
        Route::post('circulations/{circulation}/approve', 
            [\App\Http\Controllers\AdminUnit\CirculationController::class, 'approve']
        )->name('circulations.approve');

        Route::post('circulations/{circulation}/reject', 
            [\App\Http\Controllers\AdminUnit\CirculationController::class, 'reject']
        )->name('circulations.reject');

        Route::post('circulations/{circulation}/return', 
            [\App\Http\Controllers\AdminUnit\CirculationController::class, 'markReturned']
        )->name('circulations.return');

        Route::post('circulations/{circulation}/confirm-return', 
            [\App\Http\Controllers\AdminUnit\CirculationController::class, 'confirmReturn']
        )->name('circulations.confirm-return');

        // Resource dideklarasikan TERAKHIR
        Route::resource('circulations', \App\Http\Controllers\AdminUnit\CirculationController::class);
});

// =========================================================================
// 🔥🔥🔥 ROUTE GROUP: MANAGER
// =========================================================================
Route::middleware(['auth', 'role:manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {
        
        // ============================================================
        // DASHBOARD MANAGER
        // ============================================================
        Route::get('/dashboard', function () {
            $totalItems    = \App\Models\Item::count();
            $totalBorrowed = \App\Models\Circulation::where('status', 'approved')->count();
            $totalPending  = \App\Models\Circulation::where('status', 'pending')->count();
            $totalUnits    = \App\Models\Unit::count();

            $stats = [
                'total_items'    => $totalItems,
                'total_borrowed' => $totalBorrowed,
                'total_pending'  => $totalPending,
                'total_units'    => $totalUnits,
            ];

            $recentCirculations = \App\Models\Circulation::with(['item', 'user'])
                ->latest()
                ->take(5)
                ->get();

            $recentItems = \App\Models\Item::with(['unit', 'category'])
                ->latest()
                ->take(5)
                ->get();

            $itemsByUnit = \App\Models\Unit::withCount('items')
                ->where('is_active', true)
                ->get();

            return view('manager.dashboard', compact(
                'stats',
                'recentCirculations',
                'recentItems',
                'itemsByUnit'
            ));
        })->name('dashboard');
        
        // ============================================================
        // MANAJEMEN BARANG (Read-only)
        // ============================================================
        Route::get('/items', function () {
            $items = \App\Models\Item::with(['category', 'unit', 'stockCodes'])
                ->latest()
                ->paginate(15);

            $units      = \App\Models\Unit::where('is_active', true)->get();
            $categories = \App\Models\Category::all();

            return view('manager.items.index', compact('items', 'units', 'categories'));
        })->name('items.index');
        
        Route::get('/items/{item}', function (\App\Models\Item $item) {
            $item->load([
                'category', 
                'unit', 
                'fundingSource', 
                'stockCodes',
                'disposals.user',
                'disposals.approver',
                'circulations.user',
                'circulations.approver',
            ]);
            return view('manager.items.show', compact('item'));
        })->name('items.show');
        
        // ============================================================
        // SIRKULASI (Read-only)
        // ============================================================
        Route::get('/circulations', function () {
            $circulations = \App\Models\Circulation::with(['item', 'user'])
                ->latest()
                ->paginate(15);

            return view('manager.circulations.index', compact('circulations'));
        })->name('circulations.index');
        
        Route::get('/circulations/{circulation}', function (\App\Models\Circulation $circulation) {
            $circulation->load(['item', 'user', 'approver', 'itemStock']);
            return view('manager.circulations.show', compact('circulation'));
        })->name('circulations.show');

        // ============================================================
        // 🔥🔥🔥 BARU: MONITORING PENGHAPUSAN ASET (READ-ONLY)
        // ============================================================
        Route::get('/disposal-monitoring', 
            [\App\Http\Controllers\Manager\DisposalMonitoringController::class, 'index']
        )->name('disposal-monitoring.index');

        Route::get('/disposal-monitoring/{disposal}', 
            [\App\Http\Controllers\Manager\DisposalMonitoringController::class, 'show']
        )->name('disposal-monitoring.show');
});

// =========================================================================
// ROUTE GROUP: USER
// =========================================================================
Route::middleware(['auth', 'role:user'])
    ->prefix('user')
    ->name('user.')
    ->group(base_path('routes/user.php'));