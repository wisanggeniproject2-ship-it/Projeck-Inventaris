@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Riwayat Peminjaman Saya</h1>
        <a href="{{ route('user.circulations.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Ajukan Peminjaman
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-2 mb-6">
        <a href="{{ route('user.circulations.index') }}" 
           class="bg-white rounded-lg shadow p-3 text-center hover:shadow-md transition {{ !request('status') ? 'border-2 border-blue-500' : '' }}">
            <p class="text-2xl font-bold">{{ $stats['all'] }}</p>
            <p class="text-xs text-gray-500">Semua</p>
        </a>
        <a href="{{ route('user.circulations.index', ['status' => 'pending']) }}" 
           class="bg-white rounded-lg shadow p-3 text-center hover:shadow-md transition {{ request('status') == 'pending' ? 'border-2 border-yellow-500' : '' }}">
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            <p class="text-xs text-gray-500">Menunggu</p>
        </a>
        <a href="{{ route('user.circulations.index', ['status' => 'approved']) }}" 
           class="bg-white rounded-lg shadow p-3 text-center hover:shadow-md transition {{ request('status') == 'approved' ? 'border-2 border-green-500' : '' }}">
            <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</p>
            <p class="text-xs text-gray-500">Disetujui</p>
        </a>
        <a href="{{ route('user.circulations.index', ['status' => 'returned']) }}" 
           class="bg-white rounded-lg shadow p-3 text-center hover:shadow-md transition {{ request('status') == 'returned' ? 'border-2 border-blue-500' : '' }}">
            <p class="text-2xl font-bold text-blue-600">{{ $stats['returned'] }}</p>
            <p class="text-xs text-gray-500">Dikembalikan</p>
        </a>
        <a href="{{ route('user.circulations.index', ['status' => 'rejected']) }}" 
           class="bg-white rounded-lg shadow p-3 text-center hover:shadow-md transition {{ request('status') == 'rejected' ? 'border-2 border-red-500' : '' }}">
            <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
            <p class="text-xs text-gray-500">Ditolak</p>
        </a>
    </div>

    <!-- Search -->
    <div class="mb-6">
        <form method="GET" class="flex gap-2">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Cari nama barang..." 
                   class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
            @if(request('search'))
            <a href="{{ route('user.circulations.index', ['status' => request('status')]) }}" 
               class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                Reset
            </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Pinjam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tenggat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Kembali</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($circulations as $circulation)
                <tr class="border-t {{ $circulation->status == 'pending' ? 'bg-yellow-50' : '' }}">
                    <td class="px-6 py-4">
                        {{ $circulation->item->name }}
                        <br><small class="text-gray-500">{{ $circulation->item->code }}</small>
                    </td>
                    <td class="px-6 py-4">{{ $circulation->item->unit->name }}</td>
                    <td class="px-6 py-4">{{ $circulation->borrow_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        {{ $circulation->expected_return_date->format('d/m/Y') }}
                        @if($circulation->status == 'approved' && $circulation->expected_return_date < now())
                            <span class="text-red-500 text-xs block">⚠️ Terlambat</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ $circulation->return_date ? $circulation->return_date->format('d/m/Y') : '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $circulation->status == 'approved' ? 'bg-green-100 text-green-700' : 
                               ($circulation->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                               ($circulation->status == 'returned' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                            {{ ucfirst($circulation->status) }}
                        </span>
                        @if($circulation->status == 'pending')
                            <span class="ml-1 text-xs text-yellow-600">⏳</span>
                        @endif
                    </td>
                   <td class="px-6 py-4">
    <div class="flex gap-3">
        <!-- Detail Barang -->
        <a href="{{ route('user.items.show', $circulation->item_id) }}" 
           class="text-blue-600 hover:text-blue-800" title="Detail Barang">
            <i class="fas fa-eye text-lg"></i>
        </a>
        
        <!-- Kembalikan Barang (hanya untuk status approved) -->
        @if($circulation->status == 'approved')
        <form action="{{ route('user.circulations.return', $circulation) }}" method="POST" class="inline">
            @csrf
            @method('PUT')
            <button type="submit" class="text-green-600 hover:text-green-800" 
                    title="Kembalikan Barang"
                    onclick="return confirm('Yakin ingin mengembalikan barang ini?')">
                <i class="fas fa-undo-alt text-lg"></i>
            </button>
        </form>
        @endif
    </div>
</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 block"></i>
                        <p>Belum ada riwayat peminjaman</p>
                        <a href="{{ route('user.circulations.create') }}" class="text-blue-500 hover:text-blue-700 mt-2 inline-block">
                            Ajukan Peminjaman →
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $circulations->withQueryString()->links() }}
    </div>
</div>

@endsection