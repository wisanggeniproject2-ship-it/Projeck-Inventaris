<nav class="bg-white/90 backdrop-blur border-b border-gray-100 sticky top-0 z-30">
    <div class="px-4 md:px-6 py-3 flex justify-between items-center gap-4">
        <div class="flex items-center gap-3 min-w-0">
            <button id="sidebarToggle" class="text-gray-500 lg:hidden hover:text-brand-600 transition p-2 -ml-2">
                <i class="fas fa-bars text-xl"></i>
            </button>

            <!-- SEARCH BAR (dekoratif, style seperti contoh) -->
            <div class="hidden sm:flex items-center gap-2 bg-gray-100 rounded-xl px-3 py-2 w-64 md:w-96 text-gray-400">
                <i class="fas fa-magnifying-glass text-sm"></i>
                <input type="text" placeholder="Cari barang, kategori, unit, atau user..."
                       class="bg-transparent outline-none text-sm text-gray-700 w-full placeholder-gray-400">
                <kbd class="hidden md:inline text-[10px] bg-white border border-gray-200 rounded px-1.5 py-0.5 text-gray-400">⌘K</kbd>
            </div>
        </div>

        <div class="flex items-center gap-2 md:gap-3 shrink-0">

            <!-- NOTIFIKASI DROPDOWN (super_admin: seluruh sistem, admin_unit: khusus unit-nya) -->
            @if(in_array(auth()->user()->role, ['admin_unit', 'super_admin']))
                @php
                    $notifRole = auth()->user()->role;
                    $unitId = auth()->user()->unit_id;

                    $notifQuery = function() use ($notifRole, $unitId) {
                        $q = App\Models\Circulation::query();
                        if ($notifRole === 'admin_unit') {
                            $q->whereHas('item', function($qi) use ($unitId) {
                                $qi->where('unit_id', $unitId);
                            });
                        }
                        return $q;
                    };

                    $pendingCount = $notifQuery()->where('status', 'pending')->count();
                    $returnPendingCount = $notifQuery()->where('status', 'return_pending')->count();
                    $totalNotif = $pendingCount + $returnPendingCount;

                    $notifications = $notifQuery()
                        ->with(['item', 'user'])
                        ->whereIn('status', ['pending', 'return_pending'])
                        ->latest()
                        ->limit(5)
                        ->get();
                @endphp

                <div class="relative" id="notificationContainer">
                    <button id="notificationButton"
                            class="relative p-2.5 text-gray-500 hover:text-brand-600 hover:bg-brand-50 rounded-xl transition">
                        <i class="fas fa-bell text-lg"></i>
                        @if($totalNotif > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] rounded-full h-5 w-5 flex items-center justify-center animate-pulse ring-2 ring-white">
                                {{ $totalNotif > 9 ? '9+' : $totalNotif }}
                            </span>
                        @endif
                    </button>

                    <div id="notificationDropdown"
                         class="hidden absolute right-0 mt-2 w-96 bg-white rounded-2xl shadow-brand-lg border border-brand-100/60 z-50 max-h-[500px] overflow-hidden">

                        <div class="flex justify-between items-center p-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="font-semibold text-gray-800">
                                <i class="fas fa-bell text-brand-500 mr-2"></i>
                                Notifikasi
                            </h3>
                            @if($totalNotif > 0)
                                <span class="bg-red-100 text-red-600 text-xs px-2 py-1 rounded-full">
                                    {{ $totalNotif }} perlu ditangani
                                </span>
                            @else
                                <span class="bg-emerald-100 text-emerald-600 text-xs px-2 py-1 rounded-full">
                                    Semua sudah tertangani ✅
                                </span>
                            @endif
                        </div>

                        <div class="overflow-y-auto max-h-80 thin-scroll">
                            @if($notifications->count() > 0)
                                @foreach($notifications as $notif)
                                    @php
                                        $icon = $notif->status == 'pending' ? 'fas fa-clock text-amber-500' : 'fas fa-rotate-left text-blue-500';
                                        $bgColor = $notif->status == 'pending' ? 'bg-amber-50 hover:bg-amber-100' : 'bg-blue-50 hover:bg-blue-100';
                                        $statusLabel = $notif->status == 'pending' ? 'Menunggu Persetujuan' : 'Menunggu Konfirmasi Pengembalian';
                                    @endphp

                                    <a href="{{ route($notifRole . '.circulations.show', $notif) }}"
                                       class="block px-4 py-3 border-b border-gray-50 transition {{ $bgColor }}">
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
                                                        {{ $notif->status == 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }}">
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
                                    <i class="fas fa-circle-check text-4xl text-emerald-400 mb-2 block"></i>
                                    <p class="text-sm font-medium text-gray-600">Semua sudah tertangani</p>
                                    <p class="text-xs text-gray-400">Tidak ada notifikasi yang perlu ditangani</p>
                                </div>
                            @endif
                        </div>

                        <div class="p-3 border-t border-gray-100 bg-gray-50">
                            <a href="{{ route($notifRole . '.circulations.index', ['status' => 'pending']) }}"
                               class="block text-center text-sm text-brand-600 hover:text-brand-800 font-medium">
                                <i class="fas fa-arrow-right mr-1"></i>
                                Lihat Semua Sirkulasi
                            </a>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const button = document.getElementById('notificationButton');
                        const dropdown = document.getElementById('notificationDropdown');

                        if (button && dropdown) {
                            button.addEventListener('click', function(e) {
                                e.stopPropagation();
                                dropdown.classList.toggle('hidden');
                            });

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
                        class="flex items-center gap-2 focus:outline-none hover:bg-gray-100 rounded-xl pl-2 pr-3 py-1.5 transition">
                    <div class="w-9 h-9 bg-brand-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden md:block text-left leading-tight">
                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-gray-400">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</p>
                    </div>
                    <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                </button>

                <div id="userMenuDropdown"
                     class="hidden absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-brand-lg border border-brand-100/60 z-50">
                    <div class="py-2">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-800">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                <span class="px-2 py-0.5 bg-brand-50 text-brand-700 rounded-full">
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
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50 transition">
                                <i class="fas fa-arrow-right-from-bracket mr-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
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
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('appSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        }
        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }

        toggleBtn?.addEventListener('click', function () {
            sidebar.classList.contains('-translate-x-full') ? openSidebar() : closeSidebar();
        });
        overlay?.addEventListener('click', closeSidebar);
    });
</script>