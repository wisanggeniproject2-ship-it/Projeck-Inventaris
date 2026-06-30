@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Sirkulasi - {{ auth()->user()->unit->name }}</h1>
        
        <!-- NOTIFIKASI -->
        <div class="flex gap-2">
            @if($pendingCount > 0)
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-clock mr-2"></i>
                <span class="font-bold">{{ $pendingCount }}</span>
                <span class="ml-1">menunggu persetujuan</span>
            </div>
            @endif
            
            @if($returnPendingCount > 0)
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-undo-alt mr-2"></i>
                <span class="font-bold">{{ $returnPendingCount }}</span>
                <span class="ml-1">menunggu konfirmasi pengembalian</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Filter -->
    <div class="mb-6">
        <form method="GET" class="flex gap-2">
            <select name="status" class="px-4 py-2 border rounded-lg">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>🟡 Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>🟢 Approved</option>
                <option value="return_pending" {{ request('status') == 'return_pending' ? 'selected' : '' }}>🔵 Return Pending</option>
                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>📦 Returned</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>🔴 Rejected</option>
            </select>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peminjam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Pinjam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tenggat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($circulations as $circulation)
                    <tr class="hover:bg-gray-50 transition 
                        {{ $circulation->status == 'pending' ? 'bg-yellow-50' : '' }}
                        {{ $circulation->status == 'return_pending' ? 'bg-blue-50' : '' }}">
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ $circulation->item->name }}</div>
                            <div class="text-xs text-gray-500">{{ $circulation->item->code }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div>{{ $circulation->borrower_name }}</div>
                            <div class="text-xs text-gray-500">oleh: {{ $circulation->user->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs bg-gray-100 px-2 py-1 rounded">
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
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $circulation->status == 'approved' ? 'bg-green-100 text-green-700' : 
                                   ($circulation->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                   ($circulation->status == 'return_pending' ? 'bg-blue-100 text-blue-700' : 
                                   ($circulation->status == 'returned' ? 'bg-gray-100 text-gray-700' : 'bg-red-100 text-red-700'))) }}">
                                {{ $circulation->status == 'return_pending' ? 'Menunggu Konfirmasi' : ucfirst($circulation->status) }}
                            </span>
                            @if($circulation->status == 'pending')
                                <span class="ml-1 text-xs text-yellow-600">⏳</span>
                            @elseif($circulation->status == 'return_pending')
                                <span class="ml-1 text-xs text-blue-600">🔄</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2 flex-wrap">
                                <!-- Detail -->
                                <a href="{{ route('admin_unit.circulations.show', $circulation) }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <!-- Approve -->
                                @if($circulation->status == 'pending')
                                    <form action="{{ route('admin_unit.circulations.approve', $circulation) }}" 
                                          method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800" 
                                                title="Setujui Peminjaman"
                                                onclick="return confirm('Setujui peminjaman ini?')">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin_unit.circulations.reject', $circulation) }}" 
                                          method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-800"
                                                title="Tolak Peminjaman"
                                                onclick="return confirm('Tolak peminjaman ini?')">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                <!-- Confirm Return -->
                                @if($circulation->status == 'return_pending')
                                    <form action="{{ route('admin_unit.circulations.confirmReturn', $circulation) }}" 
                                          method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800"
                                                title="Konfirmasi Pengembalian"
                                                onclick="return confirm('Konfirmasi pengembalian barang ini?')">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $circulations->withQueryString()->links() }}
    </div>
</div>
@endsection