@php
    $user = Auth::user();
    $role = $user->role;
@endphp

<aside class="w-64 bg-gray-900 text-white flex flex-col sidebar-transition min-h-screen">
    <div class="p-4 border-b border-gray-800">
        <h2 class="text-xl font-bold">Inventory System</h2>
        <p class="text-sm text-gray-400">{{ ucfirst(str_replace('_', ' ', $role)) }}</p>
        @if($user->unit)
        <p class="text-xs text-gray-500">{{ $user->unit->name }}</p>
        @endif
    </div>
    
    <nav class="flex-1 p-4 overflow-y-auto">
        <ul class="space-y-2">
            
            <!-- DASHBOARD -->
            <li>
                <a href="{{ route($role . '.dashboard') }}" 
                   class="flex items-center space-x-3 px-4 py-2 rounded hover:bg-gray-800 transition {{ request()->routeIs($role . '.dashboard') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- ============ SUPER ADMIN ============ -->
            @if($role == 'super_admin')
            
            <!-- BARANG -->
            <li>
                <a href="{{ route('super_admin.items.index') }}" 
                   class="flex items-center space-x-3 px-4 py-2 rounded hover:bg-gray-800 transition {{ request()->routeIs('super_admin.items.*') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-boxes w-5"></i>
                    <span>Barang</span>
                </a>
            </li>
            
            <!-- KATEGORI -->
            <li>
                <a href="{{ route('super_admin.categories.index') }}" 
                   class="flex items-center space-x-3 px-4 py-2 rounded hover:bg-gray-800 transition {{ request()->routeIs('super_admin.categories.*') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-tags w-5"></i>
                    <span>Kategori</span>
                </a>
            </li>
            
            <!-- UNIT -->
            <li>
                <a href="{{ route('super_admin.units.index') }}" 
                   class="flex items-center space-x-3 px-4 py-2 rounded hover:bg-gray-800 transition {{ request()->routeIs('super_admin.units.*') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-building w-5"></i>
                    <span>Unit</span>
                </a>
            </li>
            
            <!-- SIRKULASI -->
            <li>
                <a href="{{ route('super_admin.circulations.index') }}" 
                   class="flex items-center space-x-3 px-4 py-2 rounded hover:bg-gray-800 transition {{ request()->routeIs('super_admin.circulations.*') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-exchange-alt w-5"></i>
                    <span>Sirkulasi</span>
                </a>
            </li>
            
            <!-- USER / MANAJEMEN AKUN -->
            <li>
                <a href="{{ route('super_admin.users.index') }}" 
                   class="flex items-center space-x-3 px-4 py-2 rounded hover:bg-gray-800 transition {{ request()->routeIs('super_admin.users.*') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-users-cog w-5"></i>
                    <span>Manajemen Akun</span>
                </a>
            </li>
            
            <!-- ============ ADMIN UNIT ============ -->
            @elseif($role == 'admin_unit')
            
            <!-- BARANG -->
            <li>
                <a href="{{ route('admin_unit.items.index') }}" 
                   class="flex items-center space-x-3 px-4 py-2 rounded hover:bg-gray-800 transition {{ request()->routeIs('admin_unit.items.*') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-boxes w-5"></i>
                    <span>Barang</span>
                </a>
            </li>
            
            <!-- SIRKULASI -->
            <li>
                <a href="{{ route('admin_unit.circulations.index') }}" 
                   class="flex items-center space-x-3 px-4 py-2 rounded hover:bg-gray-800 transition {{ request()->routeIs('admin_unit.circulations.*') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-exchange-alt w-5"></i>
                    <span>Sirkulasi</span>
                </a>
            </li>
            
            <!-- ============ MANAGER ============ -->
            @elseif($role == 'manager')
            
            <!-- BARANG -->
            <li>
                <a href="{{ route('manager.items.index') }}" 
                   class="flex items-center space-x-3 px-4 py-2 rounded hover:bg-gray-800 transition {{ request()->routeIs('manager.items.*') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-boxes w-5"></i>
                    <span>Barang</span>
                </a>
            </li>
            
            <!-- ============ USER ============ -->
            @elseif($role == 'user')
            
            <!-- BARANG -->
            <li>
                <a href="{{ route('user.items.index') }}" 
                   class="flex items-center space-x-3 px-4 py-2 rounded hover:bg-gray-800 transition {{ request()->routeIs('user.items.*') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-boxes w-5"></i>
                    <span>Barang</span>
                </a>
            </li>
            
            <!-- AJUKAN PEMINJAMAN -->
            <li>
                <a href="{{ route('user.circulations.create') }}" 
                   class="flex items-center space-x-3 px-4 py-2 rounded hover:bg-gray-800 transition {{ request()->routeIs('user.circulations.create') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-hand-paper w-5"></i>
                    <span>Ajukan Peminjaman</span>
                </a>
            </li>
            
            <!-- RIWAYAT PEMINJAMAN -->
            <li>
                <a href="{{ route('user.circulations.index') }}" 
                   class="flex items-center space-x-3 px-4 py-2 rounded hover:bg-gray-800 transition {{ request()->routeIs('user.circulations.index') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-history w-5"></i>
                    <span>Riwayat Peminjaman</span>
                </a>
            </li>
            
            @endif
            
        </ul>
    </nav>
    
    <!-- LOGOUT -->
    <div class="p-4 border-t border-gray-800">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center space-x-3 px-4 py-2 w-full rounded hover:bg-red-600 transition text-left">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>