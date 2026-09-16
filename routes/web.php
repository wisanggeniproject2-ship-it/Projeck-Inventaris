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

        // Coba login pakai email dulu. Fallback ke kolom 'username' HANYA kalau
        // kolom itu memang ada di tabel users - sebelumnya ini di-hardcode tanpa
        // dicek dulu, jadi setiap kali login gagal (salah password/email SEKALIPUN)
        // akan crash 500 karena tabel users tidak punya kolom 'username' sama sekali.
        $attempted = Auth::attempt(['email' => $request->email, 'password' => $request->password]);

        if (!$attempted && \Illuminate\Support\Facades\Schema::hasColumn('users', 'username')) {
            $attempted = Auth::attempt(['username' => $request->email, 'password' => $request->password]);
        }

        if ($attempted) {
            
            $request->session()->regenerate();
            $role = auth()->user()->role ?? '';

            // Redirect dinamis: cari route dashboard sesuai role apapun yang login
            // (super_admin, admin_unit, manager, user, dst) - bukan cuma 2 role yang di-hardcode.
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

    // PENTING: jangan langsung redirect ke /login tanpa logout dulu.
    // Kalau user masih authenticated, middleware 'guest' di route /login akan
    // otomatis melempar dia balik ke /dashboard -> jadi infinite redirect loop.
    // Logout dulu di sini supaya loop-nya pasti putus.
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
        
        // 🔥 FIX DASHBOARD: Cerdas mencari Controller atau inject data langsung
        Route::get('/dashboard', function () {
            // Jika kamu punya DashboardController, panggil itu
            if (class_exists(\App\Http\Controllers\Admin\DashboardController::class)) {
                return app(\App\Http\Controllers\Admin\DashboardController::class)->index();
            }
            
            // Fallback aman jika tidak ada Controller
            $recentCirculations = class_exists(\App\Models\Circulation::class) 
                ? \App\Models\Circulation::latest()->take(5)->get() 
                : [];
                
            return view('admin.dashboard', compact('recentCirculations'));
        })->name('dashboard');
        
        // Manajemen Barang
        Route::get('/items', [SuperAdminItemController::class, 'index'])->name('items.index');
        Route::get('/items/create', [SuperAdminItemController::class, 'create'])->name('items.create');
        Route::post('/items', [SuperAdminItemController::class, 'store'])->name('items.store');
        Route::get('/items/{item}', [SuperAdminItemController::class, 'show'])->name('items.show');
        Route::get('/items/{item}/edit', [SuperAdminItemController::class, 'edit'])->name('items.edit');
        Route::put('/items/{item}', [SuperAdminItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{item}', [SuperAdminItemController::class, 'destroy'])->name('items.destroy');
        Route::get('/items/{item}/pdf', [SuperAdminItemController::class, 'pdf'])->name('items.pdf');
        Route::get('/items/{item}/png', [SuperAdminItemController::class, 'generatePng'])->name('items.png');
        
        // Master Data
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        Route::resource('units', \App\Http\Controllers\Admin\UnitController::class);
        Route::resource('funding-sources', \App\Http\Controllers\Admin\FundingSourceController::class);
        Route::resource('circulations', \App\Http\Controllers\Admin\CirculationController::class);

        // Route tambahan ini sebelumnya HILANG - resource() standar cuma bikin
        // index/create/show/edit/dll, tidak termasuk aksi custom approve/reject/return
        // yang dipakai di admin/circulations/index.blade.php. Nama method di controller
        // ini aku samakan dengan pola yang sudah dipakai di AdminUnit\CirculationController -
        // kalau nama method aslinya beda, kabari aku, tinggal disesuaikan.
        Route::post('circulations/{circulation}/approve', [\App\Http\Controllers\Admin\CirculationController::class, 'approve'])->name('circulations.approve');
        Route::post('circulations/{circulation}/reject', [\App\Http\Controllers\Admin\CirculationController::class, 'reject'])->name('circulations.reject');
        Route::post('circulations/{circulation}/return', [\App\Http\Controllers\Admin\CirculationController::class, 'markReturned'])->name('circulations.return');
        Route::post('circulations/{circulation}/confirm-return', [\App\Http\Controllers\Admin\CirculationController::class, 'confirmReturn'])->name('circulations.confirm-return');
        
        // ============================================================
        // 🔥 FITUR NILAI ASET — halaman detail nilai aset
        // ============================================================
        Route::get('assets', [\App\Http\Controllers\Admin\AssetController::class, 'index'])->name('assets.index');
        
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
});

// =========================================================================
// ROUTE GROUP: ADMIN UNIT
// =========================================================================
Route::middleware(['auth', 'role:admin_unit'])
    ->prefix('admin-unit')
    ->name('admin_unit.')
    ->group(function () {
        
        // 🔥 FIX DASHBOARD (Admin Unit)
        Route::get('/dashboard', function () {
            if (class_exists(\App\Http\Controllers\AdminUnit\DashboardController::class)) {
                return app(\App\Http\Controllers\AdminUnit\DashboardController::class)->index();
            }
            
            $recentCirculations = class_exists(\App\Models\Circulation::class) 
                ? \App\Models\Circulation::latest()->take(5)->get() 
                : [];
                
            return view('admin_unit.dashboard', compact('recentCirculations'));
        })->name('dashboard');
        
        // Manajemen Barang
        Route::get('/items/{item}/png', [AdminUnitItemController::class, 'generatePng'])->name('items.png');
        Route::get('/items', [AdminUnitItemController::class, 'index'])->name('items.index');
        Route::get('/items/create', [AdminUnitItemController::class, 'create'])->name('items.create');
        Route::post('/items', [AdminUnitItemController::class, 'store'])->name('items.store');
        Route::get('/items/{item}', [AdminUnitItemController::class, 'show'])->name('items.show');
        Route::get('/items/{item}/pdf', [AdminUnitItemController::class, 'generatePdf'])->name('items.pdf');
        
        // ============================================================
        // SIRKULASI (mirip super_admin, tapi pakai AdminUnit controller)
        // ============================================================
        Route::resource('circulations', \App\Http\Controllers\AdminUnit\CirculationController::class);

        // ⬇️ Route custom — sama seperti super_admin tapi AdminUnit
        Route::post('circulations/{circulation}/approve', [\App\Http\Controllers\AdminUnit\CirculationController::class, 'approve'])->name('circulations.approve');
        Route::post('circulations/{circulation}/reject', [\App\Http\Controllers\AdminUnit\CirculationController::class, 'reject'])->name('circulations.reject');
        Route::post('circulations/{circulation}/return', [\App\Http\Controllers\AdminUnit\CirculationController::class, 'markReturned'])->name('circulations.return');
        Route::post('circulations/{circulation}/confirm-return', [\App\Http\Controllers\AdminUnit\CirculationController::class, 'confirmReturn'])->name('circulations.confirm-return');
});

// =========================================================================
// ROUTE GROUP: USER
// =========================================================================
// routes/user.php sebelumnya TIDAK PERNAH terdaftar sama sekali (cek
// bootstrap/app.php: cuma routes/web.php yang dipanggil), makanya semua route
// di dalamnya (termasuk user.dashboard) selalu dianggap tidak ada.
Route::middleware(['auth', 'role:user'])
    ->prefix('user')
    ->name('user.')
    ->group(base_path('routes/user.php'));