@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Sirkulasi - {{ auth()->user()->unit->name }}</h1>
        
        <!-- NOTIFIKASI PENDING -->
        @if($pendingCount > 0)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded-lg flex items-center shadow-md">
            <i class="fas fa-bell mr-2 animate-pulse"></i>
            <span class="font-bold text-lg">{{ $pendingCount }}</span>
            <span class="ml-1">peminjaman menunggu persetujuan</span>
            <a href="{{ route('admin_unit.circulations.index', ['status' => 'pending']) }}" 
               class="ml-3 text-sm bg-red-200 hover:bg-red-300 px-3 py-1 rounded">
                Lihat →
            </a>
        </div>
        @endif
    </div>

    <!-- Filter -->
    <div class="mb-6">
        <form method="GET" class="flex flex-wrap gap-2">
            <select name="status" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>🟡 Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>🟢 Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>🔴 Rejected</option>
                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>🔵 Returned</option>
            </select>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            @if(request('status'))
            <a href="{{ route('admin_unit.circulations.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition">
                <i class="fas fa-times mr-2"></i>Reset
            </a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pinjam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenggat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($circulations as $circulation)
                    <tr class="hover:bg-gray-50 transition {{ $circulation->status == 'pending' ? 'bg-yellow-50' : '' }}">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $circulation->item->name }}</div>
                            <div class="text-sm text-gray-500">{{ $circulation->item->code }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $circulation->borrower_name }}</div>
                            <div class="text-xs text-gray-500">oleh: {{ $circulation->user->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs bg-gray-100 rounded-full">
                                {{ $circulation->item->unit->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $circulation->borrow_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm">
                            {{ $circulation->expected_return_date->format('d/m/Y') }}
                            @if($circulation->status == 'approved' && $circulation->expected_return_date < now())
                                <span class="text-red-500 text-xs block font-bold">⚠️ Terlambat!</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col space-y-1">
                                <!-- Status Sirkulasi -->
                                <span class="px-2 py-1 text-xs rounded-full text-center
                                    {{ $circulation->status == 'approved' ? 'bg-green-100 text-green-700' : 
                                       ($circulation->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                       ($circulation->status == 'returned' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                                    {{ ucfirst($circulation->status) }}
                                    @if($circulation->status == 'pending')
                                        <span class="ml-1">⏳</span>
                                    @elseif($circulation->status == 'approved')
                                        <span class="ml-1">✅</span>
                                    @elseif($circulation->status == 'returned')
                                        <span class="ml-1">📦</span>
                                    @endif
                                </span>
                                
                                <!-- Status Barang -->
                                <span class="text-xs text-center
                                    {{ $circulation->item->status == 'available' ? 'text-green-600' : 'text-red-600' }}">
                                    <i class="fas fa-{{ $circulation->item->status == 'available' ? 'check-circle' : 'times-circle' }} mr-1"></i>
                                    Barang: {{ $circulation->item->status == 'available' ? 'Tersedia' : 'Dipinjam' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-2">
                                <!-- Detail -->
                                <a href="{{ route('admin_unit.circulations.show', $circulation) }}" 
                                   class="text-blue-600 hover:text-blue-800 transition" title="Detail">
                                    <i class="fas fa-eye text-lg"></i>
                                </a>
                                
                                <!-- Approve -->
                                @if($circulation->status == 'pending')
                                    <form action="{{ route('admin_unit.circulations.approve', $circulation) }}" 
                                          method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800 transition" 
                                                title="Setujui Peminjaman"
                                                onclick="return confirm('Setujui peminjaman ini? Status barang akan berubah menjadi DIPINJAM.')">
                                            <i class="fas fa-check-circle text-lg"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin_unit.circulations.reject', $circulation) }}" 
                                          method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-800 transition"
                                                title="Tolak Peminjaman"
                                                onclick="return confirm('Tolak peminjaman ini?')">
                                            <i class="fas fa-times-circle text-lg"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                <!-- Return -->
                                @if($circulation->status == 'approved')
                                    <form action="{{ route('admin_unit.circulations.return', $circulation) }}" 
                                          method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-blue-600 hover:text-blue-800 transition"
                                                title="Tandai Dikembalikan"
                                                onclick="return confirm('Tandai barang sudah dikembalikan? Status barang akan berubah menjadi TERSEDIA.')">
                                            <i class="fas fa-undo-alt text-lg"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-5xl text-gray-300 mb-4 block"></i>
                            <p class="text-lg font-medium">Belum ada data sirkulasi</p>
                            <p class="text-sm">Belum ada peminjaman di unit ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $circulations->withQueryString()->links() }}
    </div>
</div>
@endsection