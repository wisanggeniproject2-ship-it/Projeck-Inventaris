<nav class="bg-white shadow-md sticky top-0 z-40">
    <div class="px-6 py-3 flex justify-between items-center">
        <div class="flex items-center">
            <button id="sidebarToggle" class="text-gray-600 lg:hidden mr-4 hover:text-gray-800 transition">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <h1 class="text-xl font-semibold text-gray-800">Inventory Management System</h1>
        </div>
        
        <div class="flex items-center space-x-4">
            <!-- NOTIFIKASI DROPDOWN (HANYA UNTUK ADMIN UNIT) -->
            @if(auth()->user()->role == 'admin_unit')
                @php
                    $unitId = auth()->user()->unit_id;
                    
                    $pendingCount = App\Models\Circulation::whereHas('item', function($q) use ($unitId) {
                        $q->where('unit_id', $unitId);
                    })->where('status', 'pending')->count();
                    
                    $returnPendingCount = App\Models\Circulation::whereHas('item', function($q) use ($unitId) {
                        $q->where('unit_id', $unitId);
                    })->where('status', 'return_pending')->count();
                    
                    $totalNotif = $pendingCount + $returnPendingCount;
                    
                    $notifications = App\Models\Circulation::with(['item', 'user'])
                        ->whereHas('item', function($q) use ($unitId) {
                            $q->where('unit_id', $unitId);
                        })
                        ->whereIn('status', ['pending', 'return_pending'])
                        ->latest()
                        ->limit(5)
                        ->get();
                @endphp
                
                <div class="relative" id="notificationContainer">
                    <!-- Tombol Notifikasi -->
                    <button id="notificationButton" 
                            class="relative p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition">
                        <i class="fas fa-bell text-xl"></i>
                        @if($totalNotif > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
                                {{ $totalNotif > 9 ? '9+' : $totalNotif }}
                            </span>
                        @endif
                    </button>

                    <!-- Dropdown Notifikasi -->
                    <div id="notificationDropdown" 
                         class="hidden absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-2xl border border-gray-200 z-50 max-h-[500px] overflow-hidden">
                        
                        <!-- Header -->
                        <div class="flex justify-between items-center p-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="font-semibold text-gray-800">
                                <i class="fas fa-bell text-blue-500 mr-2"></i>
                                Notifikasi
                            </h3>
                            @if($totalNotif > 0)
                                <span class="bg-red-100 text-red-600 text-xs px-2 py-1 rounded-full">
                                    {{ $totalNotif }} perlu ditangani
                                </span>
                            @else
                                <span class="bg-green-100 text-green-600 text-xs px-2 py-1 rounded-full">
                                    Semua sudah tertangani ✅
                                </span>
                            @endif
                        </div>

                        <!-- List Notifikasi -->
                        <div class="overflow-y-auto max-h-80">
                            @if($notifications->count() > 0)
                                @foreach($notifications as $notif)
                                    @php
                                        $icon = $notif->status == 'pending' ? 'fas fa-clock text-yellow-500' : 'fas fa-undo-alt text-blue-500';
                                        $bgColor = $notif->status == 'pending' ? 'bg-yellow-50 hover:bg-yellow-100' : 'bg-blue-50 hover:bg-blue-100';
                                        $statusLabel = $notif->status == 'pending' ? 'Menunggu Persetujuan' : 'Menunggu Konfirmasi Pengembalian';
                                    @endphp
                                    
                                    <a href="{{ route('admin_unit.circulations.show', $notif) }}" 
                                       class="block px-4 py-3 border-b border-gray-100 transition {{ $bgColor }}">
                                        <div class="flex items-start gap-3">
                                            <div class="mt-1">
                                                <i class="{{ $icon }} text-lg"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm text-gray-800 font-medium">
                                                    {{ $notif->item->name }}
                                                </p>
                                                <p class="text-xs text-gray-600">
                                                    <span class="font-semibold">{{ $notif->borrower_name }}</span>
                                                    @if($notif->status == 'pending')
                                                        mengajukan peminjaman
                                                    @elseif($notif->status == 'return_pending')
                                                        mengajukan pengembalian
                                                    @endif
                                                </p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-xs px-2 py-0.5 rounded-full
                                                        {{ $notif->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700' }}">
                                                        {{ $statusLabel }}
                                                    </span>
                                                    <span class="text-xs text-gray-400">
                                                        {{ $notif->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                            </div>
                                            <span class="w-2 h-2 bg-red-500 rounded-full mt-2 animate-pulse"></span>
                                        </div>
                                    </a>
                                @endforeach
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fas fa-check-circle text-4xl text-green-400 mb-2 block"></i>
                                    <p class="text-sm font-medium text-gray-600">Semua sudah tertangani</p>
                                    <p class="text-xs text-gray-400">Tidak ada notifikasi yang perlu ditangani</p>
                                </div>
                            @endif
                        </div>

                        <!-- Footer -->
                        <div class="p-3 border-t border-gray-200 bg-gray-50">
                            <a href="{{ route('admin_unit.circulations.index', ['status' => 'pending']) }}" 
                               class="block text-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                                <i class="fas fa-arrow-right mr-1"></i>
                                Lihat Semua Sirkulasi
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Script untuk toggle dropdown -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const button = document.getElementById('notificationButton');
                        const dropdown = document.getElementById('notificationDropdown');
                        
                        if (button && dropdown) {
                            button.addEventListener('click', function(e) {
                                e.stopPropagation();
                                dropdown.classList.toggle('hidden');
                            });
                            
                            // Tutup dropdown saat klik di luar
                            document.addEventListener('click', function(e) {
                                const container = document.getElementById('notificationContainer');
                                if (container && !container.contains(e.target)) {
                                    dropdown.classList.add('hidden');
                                }
                            });
                        }
                    });
                </script>
            @endif

            <!-- USER MENU -->
            <div class="relative" id="userMenuContainer">
                <button id="userMenuButton" 
                        class="flex items-center space-x-2 focus:outline-none hover:bg-gray-100 rounded-lg px-3 py-2 transition">
                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span class="hidden md:inline text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                    <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                </button>
                
                <div id="userMenuDropdown" 
                     class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                    <div class="py-2">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-800">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full">
                                    {{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}
                                </span>
                            </p>
                            @if(Auth::user()->unit)
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-building mr-1"></i>
                                    {{ Auth::user()->unit->name }}
                                </p>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100 mt-1">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // User Menu Toggle
                    const userButton = document.getElementById('userMenuButton');
                    const userDropdown = document.getElementById('userMenuDropdown');
                    const userContainer = document.getElementById('userMenuContainer');
                    
                    if (userButton && userDropdown) {
                        userButton.addEventListener('click', function(e) {
                            e.stopPropagation();
                            userDropdown.classList.toggle('hidden');
                        });
                        
                        document.addEventListener('click', function(e) {
                            if (userContainer && !userContainer.contains(e.target)) {
                                userDropdown.classList.add('hidden');
                            }
                        });
                    }
                });
            </script>
        </div>
    </div>
</nav>

<style>
    [x-cloak] { display: none !important; }
</style>

<script>
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        const sidebar = document.querySelector('aside');
        sidebar.classList.toggle('-translate-x-full');
    });
</script>