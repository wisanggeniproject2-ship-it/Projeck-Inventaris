@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">📦 Daftar Barang (Semua Unit)</h1>
        <div class="text-sm text-gray-500">
            <i class="fas fa-building mr-1"></i>
            Unit Anda: {{ auth()->user()->unit->name }}
        </div>
    </div>

    <!-- Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-500 mt-1 mr-3 text-xl"></i>
            <div>
                <p class="text-sm text-blue-700 font-medium">Informasi Barang</p>
                <p class="text-sm text-blue-600">
                    <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs mr-1">🟢 Tersedia</span> = Bisa dipinjam &nbsp;|&nbsp;
                    <span class="inline-block px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs mr-1">🔴 Dipinjam</span> = Sedang dipinjam oleh orang lain
                </p>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari kode, nama, atau lokasi..." 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="unit" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Unit</option>
                    @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ request('unit') == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>🟢 Tersedia</option>
                    <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>🔴 Dipinjam</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition flex-1">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
                @if(request('search') || request('unit') || request('status'))
                <a href="{{ route('user.items.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Cards View -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($items as $item)
        <div class="bg-white rounded-lg shadow-md overflow-hidden card-hover border {{ $item->status == 'available' ? 'border-green-200' : 'border-red-200' }}">
            <div class="p-4">
                <!-- Header: Nama & Status -->
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-bold text-lg">{{ $item->name }}</h3>
                    @if($item->status == 'available')
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                            🟢 Tersedia
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">
                            🔴 Dipinjam
                        </span>
                    @endif
                </div>
                
                <!-- Info Barang -->
                <p class="text-gray-600 text-sm mb-1"><i class="fas fa-barcode mr-2"></i>{{ $item->code }}</p>
                <p class="text-gray-600 text-sm mb-1"><i class="fas fa-map-marker-alt mr-2"></i>{{ $item->location }}</p>
                <p class="text-gray-600 text-sm mb-1"><i class="fas fa-tag mr-2"></i>{{ $item->category->name }}</p>
                <p class="text-gray-600 text-sm mb-3"><i class="fas fa-building mr-2"></i>{{ $item->unit->name }}</p>
                
                <!-- Info Peminjaman Aktif -->
                @if($item->status == 'borrowed' && $item->activeCirculation)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-2 mb-3 text-xs">
                        <p class="text-red-600">
                            <i class="fas fa-user mr-1"></i> 
                            Dipinjam oleh: <strong>{{ $item->activeCirculation->borrower_name }}</strong>
                        </p>
                        <p class="text-red-500">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            Tenggat: {{ $item->activeCirculation->expected_return_date->format('d/m/Y') }}
                        </p>
                    </div>
                @endif
                
                <!-- Aksi -->
                <div class="flex gap-2">
                    <a href="{{ route('user.items.show', $item) }}" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-center px-3 py-1 rounded transition">
                        <i class="fas fa-eye mr-1"></i>Detail
                    </a>
                    @if($item->status == 'available')
                        <a href="{{ route('user.circulations.create') }}?item={{ $item->id }}" 
                           class="flex-1 bg-green-500 hover:bg-green-600 text-white text-center px-3 py-1 rounded transition">
                            <i class="fas fa-hand-paper mr-1"></i>Pinjam
                        </a>
                    @else
                        <button disabled class="flex-1 bg-gray-300 text-gray-500 text-center px-3 py-1 rounded cursor-not-allowed">
                            <i class="fas fa-times mr-1"></i>Tidak Tersedia
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 bg-gray-50 rounded-lg">
            <i class="fas fa-box-open text-6xl text-gray-400 mb-4"></i>
            <p class="text-gray-500">Tidak ada data barang</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $items->withQueryString()->links() }}
    </div>
</div>
@endsection