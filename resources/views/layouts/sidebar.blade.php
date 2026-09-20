@php
    $user = Auth::user();
    $role = $user->role;
@endphp

<aside id="appSidebar"
       class="w-72 bg-gradient-to-b from-teal-700 to-teal-800 text-white flex flex-col sidebar-transition h-screen shrink-0 fixed lg:static inset-y-0 left-0 z-50 -translate-x-full lg:translate-x-0 overflow-hidden shadow-lg">

    <!-- LOGO -->
    <div class="relative flex items-center gap-3 px-5 py-4 border-b border-white/15">
        <div class="w-16 h-16 flex items-center justify-center shrink-0">
            <img src="{{ asset('images/logopermata.png') }}" alt="Logo"
                 class="w-full h-full object-contain"
                 onerror="this.remove(); document.getElementById('logoFallback').classList.remove('hidden');">
            <i id="logoFallback" class="fas fa-graduation-cap text-white/80 text-3xl hidden"></i>
        </div>
        <div class="leading-tight min-w-0">
            <p class="font-bold text-white truncate tracking-wide">Yayasan Permata</p>
            <p class="text-[11px] text-teal-200 truncate">Sistem Manajemen Inventaris</p>
        </div>
    </div>

    <nav class="relative z-10 flex-1 px-3 py-4 overflow-y-auto thin-scroll">
        <ul class="space-y-1">

            {{-- MENU UTAMA --}}
            <li class="px-3 pb-1 pt-1 text-[11px] font-semibold tracking-wider text-teal-200/70 uppercase">Menu Utama</li>
            <li>
                <a href="{{ route($role . '.dashboard') }}"
                   class="menu-item group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs($role . '.dashboard')
                                ? 'bg-white/20 text-orange-400 shadow-lg shadow-black/10'
                                : 'text-white/80 hover:bg-white/20 hover:text-orange-400' }}">
                    <i class="fas fa-gauge-high w-5 text-center transition-colors"></i>
                    <span class="text-sm font-medium transition-colors">Dashboard</span>
                </a>
            </li>

            {{-- ============ SUPER ADMIN ============ --}}
            @if($role == 'super_admin')

            <li class="px-3 pb-1 pt-4 text-[11px] font-semibold tracking-wider text-teal-200/70 uppercase">Data Master</li>

            <li>
                <a href="{{ route('super_admin.items.index') }}"
                   class="menu-item group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('super_admin.items.*')
                                ? 'bg-white/20 text-orange-400 shadow-lg shadow-black/10'
                                : 'text-white/80 hover:bg-white/20 hover:text-orange-400' }}">
                    <i class="fas fa-boxes-stacked w-5 text-center transition-colors"></i>
                    <span class="text-sm font-medium transition-colors">Barang</span>
                </a>
            </li>

            <li>
                <a href="{{ route('super_admin.categories.index') }}"
                   class="menu-item group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('super_admin.categories.*')
                                ? 'bg-white/20 text-orange-400 shadow-lg shadow-black/10'
                                : 'text-white/80 hover:bg-white/20 hover:text-orange-400' }}">
                    <i class="fas fa-tags w-5 text-center transition-colors"></i>
                    <span class="text-sm font-medium transition-colors">Kategori</span>
                </a>
            </li>

            <li>
                <a href="{{ route('super_admin.units.index') }}"
                   class="menu-item group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('super_admin.units.*')
                                ? 'bg-white/20 text-orange-400 shadow-lg shadow-black/10'
                                : 'text-white/80 hover:bg-white/20 hover:text-orange-400' }}">
                    <i class="fas fa-building w-5 text-center transition-colors"></i>
                    <span class="text-sm font-medium transition-colors">Unit</span>
                </a>
            </li>

            {{-- 🔥 SUMBER DANA — ikon hand-holding-dollar + animasi wiggle --}}
            <li>
                <a href="{{ route('super_admin.funding-sources.index') }}"
                   class="menu-item group relative flex items-center gap-3 px-3 py-2.5 rounded-2xl transition-all overflow-hidden
                          {{ request()->routeIs('super_admin.funding-sources.*')
                                ? 'bg-white/20 text-orange-400 shadow-lg shadow-black/10'
                                : 'text-white/80 hover:bg-white/20 hover:text-orange-400' }}">

                    {{-- Efek kilau melintas saat hover --}}
                    <span class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-out bg-gradient-to-r from-transparent via-white/15 to-transparent pointer-events-none"></span>

                    {{-- Ikon: hand-holding-dollar + animasi wiggle --}}
                    <i class="fas fa-hand-holding-dollar w-5 text-center animate-wiggle-slow relative z-10 transition-colors"></i>

                    <span class="text-sm font-medium relative z-10 transition-colors">Sumber Dana</span>
                </a>
            </li>

            {{-- ============================================================ --}}
            {{-- 🔥 NILAI ASET — DISAMAKAN DENGAN SUMBER DANA                  --}}
            {{-- ============================================================ --}}
            <li>
                <a href="{{ route('super_admin.assets.index') }}"
                   class="menu-item group relative flex items-center gap-3 px-3 py-2.5 rounded-2xl transition-all overflow-hidden
                          {{ request()->routeIs('super_admin.assets.*')
                                ? 'bg-white/20 text-orange-400 shadow-lg shadow-black/10'
                                : 'text-white/80 hover:bg-white/20 hover:text-orange-400' }}">

                    {{-- Efek kilau melintas saat hover --}}
                    <span class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-out bg-gradient-to-r from-transparent via-white/15 to-transparent pointer-events-none"></span>

                    {{-- Ikon koin + animasi bounce --}}
                    <i class="fas fa-coins w-5 text-center animate-bounce-slow relative z-10 transition-colors"></i>

                    <span class="text-sm font-medium relative z-10 transition-colors">Nilai Aset</span>
                </a>
            </li>
            {{-- 🔥 END MENU NILAI ASET --}}

            <li class="px-3 pb-1 pt-4 text-[11px] font-semibold tracking-wider text-teal-200/70 uppercase">Transaksi</li>

            <li>
                <a href="{{ route('super_admin.circulations.index') }}"
                   class="menu-item group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('super_admin.circulations.*')
                                ? 'bg-white/20 text-orange-400 shadow-lg shadow-black/10'
                                : 'text-white/80 hover:bg-white/20 hover:text-orange-400' }}">
                    <i class="fas fa-right-left w-5 text-center transition-colors"></i>
                    <span class="text-sm font-medium transition-colors">Sirkulasi</span>
                </a>
            </li>

            {{-- 🔥 PENGAJUAN PENGHAPUSAN ASET --}}
            <li>
                @php $pendingDisposalCount = \App\Models\AssetDisposal::where('status', 'pending')->count(); @endphp
                <a href="{{ route('super_admin.disposals.index') }}"
                   class="menu-item group flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('super_admin.disposals.*')
                                ? 'bg-white/20 text-orange-400 shadow-lg shadow-black/10'
                                : 'text-white/80 hover:bg-white/20 hover:text-orange-400' }}">
                    <span class="flex items-center gap-3">
                        <i class="fas fa-trash-can w-5 text-center transition-colors"></i>
                        <span class="text-sm font-medium transition-colors">Pengajuan Aset</span>
                    </span>
                    @if($pendingDisposalCount > 0)
                        <span class="bg-red-500 text-white text-[10px] font-bold rounded-full h-5 min-w-5 px-1 flex items-center justify-center animate-pulse">
                            {{ $pendingDisposalCount }}
                        </span>
                    @endif
                </a>
            </li>

            <li class="px-3 pb-1 pt-4 text-[11px] font-semibold tracking-wider text-teal-200/70 uppercase">Sistem</li>

            <li>
                <a href="{{ route('super_admin.users.index') }}"
                   class="menu-item group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('super_admin.users.*')
                                ? 'bg-white/20 text-orange-400 shadow-lg shadow-black/10'
                                : 'text-white/80 hover:bg-white/20 hover:text-orange-400' }}">
                    <i class="fas fa-user-gear w-5 text-center transition-colors"></i>
                    <span class="text-sm font-medium transition-colors">Manajemen Akun</span>
                </a>
            </li>

            {{-- ============ ADMIN UNIT ============ --}}
            @elseif($role == 'admin_unit')

            <li class="px-3 pb-1 pt-4 text-[11px] font-semibold tracking-wider text-teal-200/70 uppercase">Data Master</li>
            <li>
                <a href="{{ route('admin_unit.items.index') }}"
                   class="menu-item group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('admin_unit.items.*')
                                ? 'bg-white/20 text-orange-400 shadow-lg shadow-black/10'
                                : 'text-white/80 hover:bg-white/20 hover:text-orange-400' }}">
                    <i class="fas fa-boxes-stacked w-5 text-center transition-colors"></i>
                    <span class="text-sm font-medium transition-colors">Barang</span>
                </a>
            </li>

            <li class="px-3 pb-1 pt-4 text-[11px] font-semibold tracking-wider text-teal-200/70 uppercase">Transaksi</li>
            <li>
                <a href="{{ route('admin_unit.circulations.index') }}"
                   class="menu-item group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('admin_unit.circulations.*')
                                ? 'bg-white/20 text-orange-400 shadow-lg shadow-black/10'
                                : 'text-white/80 hover:bg-white/20 hover:text-orange-400' }}">
                    <i class="fas fa-right-left w-5 text-center transition-colors"></i>
                    <span class="text-sm font-medium transition-colors">Sirkulasi</span>
                </a>
            </li>

            {{-- ============ MANAGER ============ --}}
            @elseif($role == 'manager')

            <li class="px-3 pb-1 pt-4 text-[11px] font-semibold tracking-wider text-teal-200/70 uppercase">Data Master</li>
            <li>
                <a href="{{ route('manager.items.index') }}"
                   class="menu-item group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('manager.items.*')
                                ? 'bg-white/20 text-orange-400 shadow-lg shadow-black/10'
                                : 'text-white/80 hover:bg-white/20 hover:text-orange-400' }}">
                    <i class="fas fa-boxes-stacked w-5 text-center transition-colors"></i>
                    <span class="text-sm font-medium transition-colors">Barang</span>
                </a>
            </li>

            {{-- ============ USER ============ --}}
            @elseif($role == 'user')

            <li class="px-3 pb-1 pt-4 text-[11px] font-semibold tracking-wider text-teal-200/70 uppercase">Data Master</li>
            <li>
                <a href="{{ route('user.items.index') }}"
                   class="menu-item group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('user.items.*')
                                ? 'bg-white/20 text-orange-400 shadow-lg shadow-black/10'
                                : 'text-white/80 hover:bg-white/20 hover:text-orange-400' }}">
                    <i class="fas fa-boxes-stacked w-5 text-center transition-colors"></i>
                    <span class="text-sm font-medium transition-colors">Barang</span>
                </a>
            </li>

            <li class="px-3 pb-1 pt-4 text-[11px] font-semibold tracking-wider text-teal-200/70 uppercase">Transaksi</li>
            <li>
                <a href="{{ route('user.circulations.create') }}"
                   class="menu-item group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('user.circulations.create')
                                ? 'bg-white/20 text-orange-400 shadow-lg shadow-black/10'
                                : 'text-white/80 hover:bg-white/20 hover:text-orange-400' }}">
                    <i class="fas fa-hand-holding w-5 text-center transition-colors"></i>
                    <span class="text-sm font-medium transition-colors">Ajukan Peminjaman</span>
                </a>
            </li>
            <li>
                <a href="{{ route('user.circulations.index') }}"
                   class="menu-item group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('user.circulations.index')
                                ? 'bg-white/20 text-orange-400 shadow-lg shadow-black/10'
                                : 'text-white/80 hover:bg-white/20 hover:text-orange-400' }}">
                    <i class="fas fa-clock-rotate-left w-5 text-center transition-colors"></i>
                    <span class="text-sm font-medium transition-colors">Riwayat Peminjaman</span>
                </a>
            </li>

            {{-- 🔥 PENGAJUAN ASET (PENGHAPUSAN BARANG RUSAK) --}}
            <li>
                <a href="{{ route('user.disposals.create') }}"
                   class="menu-item group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('user.disposals.create')
                                ? 'bg-white/20 text-orange-400 shadow-lg shadow-black/10'
                                : 'text-white/80 hover:bg-white/20 hover:text-orange-400' }}">
                    <i class="fas fa-trash-can w-5 text-center transition-colors"></i>
                    <span class="text-sm font-medium transition-colors">Ajukan Penghapusan</span>
                </a>
            </li>
            <li>
                <a href="{{ route('user.disposals.index') }}"
                   class="menu-item group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('user.disposals.index')
                                ? 'bg-white/20 text-orange-400 shadow-lg shadow-black/10'
                                : 'text-white/80 hover:bg-white/20 hover:text-orange-400' }}">
                    <i class="fas fa-clipboard-list w-5 text-center transition-colors"></i>
                    <span class="text-sm font-medium transition-colors">Riwayat Pengajuan</span>
                </a>
            </li>

            @endif

        </ul>

        {{-- ============================================================ --}}
        {{-- 🔥 NILAI ASET — CARD RINGKASAN (HANYA UNTUK SUPER ADMIN)     --}}
        {{-- ============================================================ --}}
        @if($role === 'super_admin')
        @php
            $totalNilaiAset = 0;
            $totalJumlahBarang = 0;
            $perKategori = [];

            if (class_exists(\App\Models\Item::class)) {
                $items = \App\Models\Item::with('category')->get();

                foreach ($items as $item) {
                    $harga = (float) ($item->price ?? 0);
                    $stok  = (int)   ($item->stock ?? 1);
                    $subtotal = $harga * $stok;

                    $totalNilaiAset    += $subtotal;
                    $totalJumlahBarang += $stok;

                    $namaKategori = optional($item->category)->name ?? 'Tanpa Kategori';

                    if (!isset($perKategori[$namaKategori])) {
                        $perKategori[$namaKategori] = ['jumlah' => 0, 'nilai' => 0];
                    }

                    $perKategori[$namaKategori]['jumlah'] += $stok;
                    $perKategori[$namaKategori]['nilai']  += $subtotal;
                }

                uasort($perKategori, fn($a, $b) => $b['nilai'] <=> $a['nilai']);
            }
        @endphp

        <div class="mt-6 mx-0 rounded-2xl bg-gradient-to-br from-amber-500/20 via-amber-400/10 to-transparent border border-amber-300/30 overflow-hidden">

            {{-- HEADER --}}
            <div class="flex items-center gap-2 px-4 py-3 border-b border-amber-300/20 bg-amber-500/10">
                <div class="w-8 h-8 rounded-lg bg-amber-400/20 flex items-center justify-center shrink-0">
                    <i class="fas fa-coins text-amber-300 text-sm animate-bounce-slow"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-white font-bold text-sm leading-tight">Nilai Aset</p>
                    <p class="text-amber-200/80 text-[10px] leading-tight">Total kekayaan inventaris</p>
                </div>
            </div>

            {{-- TOTAL NILAI ASET + JUMLAH BARANG --}}
            <div class="px-4 py-3 border-b border-amber-300/10">
                <p class="text-[10px] uppercase tracking-wider text-amber-200/70 mb-1">Total Nilai Aset</p>
                <p class="text-xl font-bold text-white leading-tight">
                    Rp {{ number_format($totalNilaiAset, 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-amber-100/80 mt-1">
                    <i class="fas fa-boxes-stacked mr-1 text-amber-300"></i>
                    {{ number_format($totalJumlahBarang, 0, ',', '.') }} unit barang
                </p>
            </div>

            {{-- PER KATEGORI --}}
            <div class="px-4 py-3 border-b border-amber-300/10">
                <p class="text-[10px] uppercase tracking-wider text-amber-200/70 mb-2">Per Kategori</p>

                @if(empty($perKategori))
                    <p class="text-[11px] text-amber-100/60 italic">Belum ada data barang</p>
                @else
                    <div class="space-y-2 max-h-48 overflow-y-auto thin-scroll pr-1">
                        @foreach($perKategori as $nama => $data)
                        <div class="flex items-start justify-between gap-2 text-[11px]">
                            <div class="min-w-0 flex-1">
                                <p class="text-white font-medium truncate" title="{{ $nama }}">{{ $nama }}</p>
                                <p class="text-amber-200/70 text-[10px]">{{ number_format($data['jumlah'], 0, ',', '.') }} unit</p>
                            </div>
                            <p class="text-amber-100 font-semibold whitespace-nowrap text-right">
                                Rp {{ number_format($data['nilai'], 0, ',', '.') }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- TOTAL DI BAWAH --}}
            <div class="px-4 py-3 bg-amber-500/10">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] uppercase tracking-wider text-amber-200/90 font-semibold">Total Keseluruhan</p>
                    <p class="text-sm font-bold text-white">Rp {{ number_format($totalNilaiAset, 0, ',', '.') }}</p>
                </div>
            </div>

        </div>
        @endif
        {{-- ============================================================ --}}
        {{-- END NILAI ASET --}}
        {{-- ============================================================ --}}

    </nav>

    <!-- USER + PROFIL + LOGOUT -->
    <div class="relative z-10 p-4 border-t border-white/10">
        <div class="flex items-center gap-3 px-1 pb-3">
            <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-white text-sm font-bold shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="leading-tight min-w-0">
                <p class="text-sm font-semibold text-white truncate">{{ $user->name }}</p>
                <p class="text-[11px] text-teal-200 truncate">{{ ucfirst(str_replace('_', ' ', $role)) }}</p>
            </div>
        </div>

        <a href="{{ route('profile.edit') }}"
           class="flex items-center gap-3 px-3 py-2.5 w-full rounded-xl transition-all mb-1
                  {{ request()->routeIs('profile.edit')
                        ? 'bg-white/20 text-orange-400'
                        : 'text-teal-100 hover:bg-white/15 hover:text-white' }}">
            <i class="fas fa-user w-5 text-center"></i>
            <span class="text-sm font-medium">Profil Saya</span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="flex items-center gap-3 px-3 py-2.5 w-full rounded-xl text-teal-100 hover:bg-red-500/90 hover:text-white transition-all text-left">
                <i class="fas fa-arrow-right-from-bracket w-5 text-center"></i>
                <span class="text-sm font-medium">Logout</span>
            </button>
        </form>
    </div>
</aside>

<!-- Overlay untuk mobile -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/40 z-40 hidden lg:hidden"></div>

{{-- ============================================================ --}}
{{-- 🔥 STYLE: Animasi untuk menu Sumber Dana & Nilai Aset        --}}
{{-- ============================================================ --}}
@push('styles')
<style>
    /* ===== Animasi bounce halus — ikon koin (Nilai Aset) ===== */
    @keyframes bounceSlow {
        0%, 100% { transform: translateY(0); }
        50%      { transform: translateY(-3px); }
    }

    .animate-bounce-slow {
        animation: bounceSlow 1.8s ease-in-out infinite;
        display: inline-block;
    }

    .group:hover .animate-bounce-slow {
        animation: bounceSlow 0.6s ease-in-out infinite;
    }

    /* ===== Animasi wiggle — ikon Sumber Dana ===== */
    @keyframes wiggleSlow {
        0%, 100% { transform: rotate(0deg); }
        25%      { transform: rotate(-8deg); }
        75%      { transform: rotate(8deg); }
    }

    .animate-wiggle-slow {
        animation: wiggleSlow 2.5s ease-in-out infinite;
        display: inline-block;
        transform-origin: center;
    }

    .group:hover .animate-wiggle-slow {
        animation: wiggleSlow 0.8s ease-in-out infinite;
    }

    /* ===== Pulse halus — ikon sparkles (Nilai Aset) ===== */
    @keyframes pulseSoft {
        0%, 100% { opacity: 0.5; transform: scale(1); }
        50%      { opacity: 1; transform: scale(1.15); }
    }

    .animate-pulse-soft {
        animation: pulseSoft 2s ease-in-out infinite;
        display: inline-block;
    }
</style>
@endpush