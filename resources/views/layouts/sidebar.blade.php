@php
    $user = Auth::user();
    $role = $user->role;
@endphp

<aside id="appSidebar"
       class="relative w-72 bg-brand-400 text-white flex flex-col sidebar-transition h-screen shrink-0 fixed lg:static inset-y-0 left-0 z-50 -translate-x-full lg:translate-x-0 overflow-hidden shadow-brand-lg">

    <!-- LOGO -->
    <div class="relative flex items-center gap-3 px-5 py-4 border-b border-white/15">
        <div class="w-16 h-16 flex items-center justify-center shrink-0">
            <img src="{{ asset('images/logopermata.png') }}" alt="Logo"
                 class="w-full h-full object-contain"
                 onerror="this.remove(); document.getElementById('logoFallback').classList.remove('hidden');">
            <i id="logoFallback" class="fas fa-graduation-cap text-white text-3xl hidden"></i>
        </div>
        <div class="leading-tight min-w-0">
            <p class="font-bold text-white truncate tracking-wide">{{ config('app.name', 'Inventaris Sekolah') }}</p>
            <p class="text-[11px] text-white/70 truncate">Sistem Manajemen Inventaris</p>
        </div>
    </div>

    <nav class="relative z-10 flex-1 px-3 py-4 overflow-y-auto thin-scroll">
        <ul class="space-y-1">

            {{-- MENU UTAMA --}}
            <li class="px-3 pb-1 pt-1 text-[11px] font-semibold tracking-wider text-white/60 uppercase">Menu Utama</li>
            <li>
                <a href="{{ route($role . '.dashboard') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs($role . '.dashboard')
                                ? 'bg-brand-600 text-white shadow-brand'
                                : 'text-white/85 hover:bg-brand-600 hover:text-white' }}">
                    <i class="fas fa-gauge-high w-5 text-center"></i>
                    <span class="text-sm font-medium">Dashboard</span>
                </a>
            </li>

            {{-- ============ SUPER ADMIN ============ --}}
            @if($role == 'super_admin')

            <li class="px-3 pb-1 pt-4 text-[11px] font-semibold tracking-wider text-white/60 uppercase">Data Master</li>

            <li>
                <a href="{{ route('super_admin.items.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('super_admin.items.*')
                                ? 'bg-brand-600 text-white shadow-brand'
                                : 'text-white/85 hover:bg-brand-600 hover:text-white' }}">
                    <i class="fas fa-boxes-stacked w-5 text-center"></i>
                    <span class="text-sm font-medium">Barang</span>
                </a>
            </li>

            <li>
                <a href="{{ route('super_admin.categories.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('super_admin.categories.*')
                                ? 'bg-brand-600 text-white shadow-brand'
                                : 'text-white/85 hover:bg-brand-600 hover:text-white' }}">
                    <i class="fas fa-tags w-5 text-center"></i>
                    <span class="text-sm font-medium">Kategori</span>
                </a>
            </li>

            <li>
                <a href="{{ route('super_admin.units.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('super_admin.units.*')
                                ? 'bg-brand-600 text-white shadow-brand'
                                : 'text-white/85 hover:bg-brand-600 hover:text-white' }}">
                    <i class="fas fa-building w-5 text-center"></i>
                    <span class="text-sm font-medium">Unit</span>
                </a>
            </li>

            <li>
                <a href="{{ route('super_admin.funding-sources.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('super_admin.funding-sources.*')
                                ? 'bg-brand-600 text-white shadow-brand'
                                : 'text-white/85 hover:bg-brand-600 hover:text-white' }}">
                    <i class="fas fa-coins w-5 text-center"></i>
                    <span class="text-sm font-medium">Sumber Dana</span>
                </a>
            </li>

            <li class="px-3 pb-1 pt-4 text-[11px] font-semibold tracking-wider text-white/60 uppercase">Transaksi</li>

            <li>
                <a href="{{ route('super_admin.circulations.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('super_admin.circulations.*')
                                ? 'bg-brand-600 text-white shadow-brand'
                                : 'text-white/85 hover:bg-brand-600 hover:text-white' }}">
                    <i class="fas fa-right-left w-5 text-center"></i>
                    <span class="text-sm font-medium">Sirkulasi</span>
                </a>
            </li>

            <li class="px-3 pb-1 pt-4 text-[11px] font-semibold tracking-wider text-white/60 uppercase">Sistem</li>

            <li>
                <a href="{{ route('super_admin.users.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('super_admin.users.*')
                                ? 'bg-brand-600 text-white shadow-brand'
                                : 'text-white/85 hover:bg-brand-600 hover:text-white' }}">
                    <i class="fas fa-user-gear w-5 text-center"></i>
                    <span class="text-sm font-medium">Manajemen Akun</span>
                </a>
            </li>

            {{-- ============ ADMIN UNIT ============ --}}
            @elseif($role == 'admin_unit')

            <li class="px-3 pb-1 pt-4 text-[11px] font-semibold tracking-wider text-white/60 uppercase">Data Master</li>
            <li>
                <a href="{{ route('admin_unit.items.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('admin_unit.items.*')
                                ? 'bg-brand-600 text-white shadow-brand'
                                : 'text-white/85 hover:bg-brand-600 hover:text-white' }}">
                    <i class="fas fa-boxes-stacked w-5 text-center"></i>
                    <span class="text-sm font-medium">Barang</span>
                </a>
            </li>

            <li class="px-3 pb-1 pt-4 text-[11px] font-semibold tracking-wider text-white/60 uppercase">Transaksi</li>
            <li>
                <a href="{{ route('admin_unit.circulations.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('admin_unit.circulations.*')
                                ? 'bg-brand-600 text-white shadow-brand'
                                : 'text-white/85 hover:bg-brand-600 hover:text-white' }}">
                    <i class="fas fa-right-left w-5 text-center"></i>
                    <span class="text-sm font-medium">Sirkulasi</span>
                </a>
            </li>

            {{-- ============ MANAGER ============ --}}
            @elseif($role == 'manager')

            <li class="px-3 pb-1 pt-4 text-[11px] font-semibold tracking-wider text-white/60 uppercase">Data Master</li>
            <li>
                <a href="{{ route('manager.items.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('manager.items.*')
                                ? 'bg-brand-600 text-white shadow-brand'
                                : 'text-white/85 hover:bg-brand-600 hover:text-white' }}">
                    <i class="fas fa-boxes-stacked w-5 text-center"></i>
                    <span class="text-sm font-medium">Barang</span>
                </a>
            </li>

            {{-- ============ USER ============ --}}
            @elseif($role == 'user')

            <li class="px-3 pb-1 pt-4 text-[11px] font-semibold tracking-wider text-white/60 uppercase">Data Master</li>
            <li>
                <a href="{{ route('user.items.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('user.items.*')
                                ? 'bg-brand-600 text-white shadow-brand'
                                : 'text-white/85 hover:bg-brand-600 hover:text-white' }}">
                    <i class="fas fa-boxes-stacked w-5 text-center"></i>
                    <span class="text-sm font-medium">Barang</span>
                </a>
            </li>

            <li class="px-3 pb-1 pt-4 text-[11px] font-semibold tracking-wider text-white/60 uppercase">Transaksi</li>
            <li>
                <a href="{{ route('user.circulations.create') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('user.circulations.create')
                                ? 'bg-brand-600 text-white shadow-brand'
                                : 'text-white/85 hover:bg-brand-600 hover:text-white' }}">
                    <i class="fas fa-hand-holding w-5 text-center"></i>
                    <span class="text-sm font-medium">Ajukan Peminjaman</span>
                </a>
            </li>
            <li>
                <a href="{{ route('user.circulations.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all
                          {{ request()->routeIs('user.circulations.index')
                                ? 'bg-brand-600 text-white shadow-brand'
                                : 'text-white/85 hover:bg-brand-600 hover:text-white' }}">
                    <i class="fas fa-clock-rotate-left w-5 text-center"></i>
                    <span class="text-sm font-medium">Riwayat Peminjaman</span>
                </a>
            </li>

            @endif

        </ul>
    </nav>

    <!-- USER + LOGOUT -->
    <div class="relative z-10 p-4 border-t border-white/10">
        <div class="flex items-center gap-3 px-1 pb-3">
            <div class="w-9 h-9 rounded-full bg-brand-800 flex items-center justify-center text-white text-sm font-bold shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="leading-tight min-w-0">
                <p class="text-sm font-semibold text-white truncate">{{ $user->name }}</p>
                <p class="text-[11px] text-white/60 truncate">{{ ucfirst(str_replace('_', ' ', $role)) }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="flex items-center gap-3 px-3 py-2.5 w-full rounded-xl text-brand-100 hover:bg-red-500/90 hover:text-white transition-all text-left">
                <i class="fas fa-arrow-right-from-bracket w-5 text-center"></i>
                <span class="text-sm font-medium">Logout</span>
            </button>
        </form>
    </div>
</aside>

<!-- Overlay untuk mobile -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/40 z-40 hidden lg:hidden"></div>