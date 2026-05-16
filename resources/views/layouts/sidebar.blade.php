@php
    $user = Auth::user();
@endphp

<aside class="w-64 bg-gray-900 text-white flex flex-col sidebar-transition">
    <div class="p-4 border-b border-gray-800">
        <h2 class="text-xl font-bold">Inventory System</h2>
        <p class="text-sm text-gray-400">{{ ucfirst($user->role) }} Panel</p>
    </div>
    
    <nav class="flex-1 p-4">
        <ul class="space-y-2">
            <li>
                <a href="{{ route($user->role . '.dashboard') }}" class="flex items-center space-x-3 px-4 py-2 rounded hover:bg-gray-800 transition">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route($user->role . '.items.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded hover:bg-gray-800 transition">
                    <i class="fas fa-boxes"></i>
                    <span>Barang</span>
                </a>
            </li>
            
            @if($user->role == 'admin')
                <li>
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded hover:bg-gray-800 transition">
                        <i class="fas fa-tags"></i>
                        <span>Kategori</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('admin.units.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded hover:bg-gray-800 transition">
                        <i class="fas fa-building"></i>
                        <span>Unit</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('admin.circulations.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded hover:bg-gray-800 transition">
                        <i class="fas fa-exchange-alt"></i>
                        <span>Sirkulasi</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded hover:bg-gray-800 transition">
                        <i class="fas fa-users"></i>
                        <span>User</span>
                    </a>
                </li>
            @elseif($user->role == 'manager')
                <li>
                    <a href="{{ route('manager.circulations.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded hover:bg-gray-800 transition">
                        <i class="fas fa-exchange-alt"></i>
                        <span>Sirkulasi</span>
                    </a>
                </li>
            @else
                <li>
                    <a href="{{ route('user.circulations.create') }}" class="flex items-center space-x-3 px-4 py-2 rounded hover:bg-gray-800 transition">
                        <i class="fas fa-hand-paper"></i>
                        <span>Ajukan Peminjaman</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.circulations.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded hover:bg-gray-800 transition">
                        <i class="fas fa-list"></i>
                        <span>Riwayat Peminjaman</span>
                    </a>
                </li>
            @endif
        </ul>
    </nav>
</aside>