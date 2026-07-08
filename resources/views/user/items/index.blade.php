@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
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
                    <span class="inline-block px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs mr-1">🔴 Tidak Tersedia</span> = Dipinjam / Rusak / Perbaikan &nbsp;|&nbsp;
                    <span class="inline-block px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs mr-1">📦 Stok Habis</span> = Stok 0
                </p>
            </div>
        </div>
    </div>

    <!-- Filter TABS -->
    <div class="bg-white rounded-lg shadow mb-6 overflow-x-auto">
        <div class="border-b border-gray-200 min-w-max">
            <nav class="flex">
                <a href="{{ route('user.items.index') }}" 
                   class="inline-flex items-center px-4 py-3 border-b-2 text-sm font-medium transition whitespace-nowrap
                   {{ !request('status') && !request('filter') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-list mr-2"></i>Semua
                </a>
                <a href="{{ route('user.items.index', ['status' => 'available']) }}" 
                   class="inline-flex items-center px-4 py-3 border-b-2 text-sm font-medium transition whitespace-nowrap
                   {{ request('status') == 'available' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>Tersedia
                </a>
                <a href="{{ route('user.items.index', ['status' => 'unavailable']) }}" 
                   class="inline-flex items-center px-4 py-3 border-b-2 text-sm font-medium transition whitespace-nowrap
                   {{ request('status') == 'unavailable' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-times-circle text-red-500 mr-2"></i>Tidak Tersedia
                </a>
                <a href="{{ route('user.items.index', ['filter' => 'my']) }}" 
                   class="inline-flex items-center px-4 py-3 border-b-2 text-sm font-medium transition whitespace-nowrap
                   {{ request('filter') == 'my' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-user mr-2"></i>Pinjamanku
                </a>
            </nav>
        </div>
    </div>

    <!-- Filter Dropdowns -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('user.items.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if(request('filter'))
                <input type="hidden" name="filter" value="{{ request('filter') }}">
            @endif

            <div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="🔍 Cari kode, nama, lokasi..." 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="unit" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">🏢 Semua Unit</option>
                    @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ request('unit') == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="category" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">📂 Semua Kategori</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
                @if(request()->anyFilled(['search', 'unit', 'category', 'status', 'filter']))
                <a href="{{ route('user.items.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Cards View dengan Gambar -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($items as $item)
        @php
            $canBorrow = $item->canBeBorrowed();
            $isStockEmpty = $item->stock <= 0;
            $isBorrowed = $item->status == 'borrowed';
            $isBroken = $item->isBroken();
            $activeCirculation = $item->activeCirculation;
            
            // Tentukan status untuk badge
            if ($canBorrow) {
                $statusBadge = '🟢 Tersedia';
                $statusColor = 'bg-green-500';
            } elseif ($isStockEmpty && $item->status == 'available') {
                $statusBadge = '📦 Stok Habis';
                $statusColor = 'bg-yellow-500';
            } elseif ($isBorrowed) {
                $statusBadge = '🔴 Dipinjam';
                $statusColor = 'bg-red-500';
            } elseif ($isBroken) {
                $statusBadge = $item->condition == 'rusak' ? '🔧 Rusak' : '🛠 Perbaikan';
                $statusColor = 'bg-gray-500';
            } else {
                $statusBadge = '🔴 Tidak Tersedia';
                $statusColor = 'bg-red-500';
            }
        @endphp
        <div class="bg-white rounded-lg shadow-md overflow-hidden card-hover border 
            {{ $canBorrow ? 'border-green-200 hover:shadow-xl' : 'border-red-200 hover:shadow-xl' }}">
            
            <!-- GAMBAR BARANG -->
            <div class="relative h-48 bg-gray-100 overflow-hidden">
                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" 
                     class="w-full h-full object-cover transition hover:scale-105 duration-300">
                <div class="absolute top-2 right-2">
                    <span class="px-2 py-1 text-xs rounded-full {{ $statusColor }} text-white shadow">
                        {{ $statusBadge }}
                    </span>
                </div>
                @if($isBorrowed || $isStockEmpty)
                    <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
                        <i class="fas fa-lock text-5xl text-white/70"></i>
                    </div>
                @endif
            </div>
            
            <div class="p-4">
                <!-- Nama & Tags -->
                <h3 class="font-bold text-lg mb-1">{{ $item->name }}</h3>
                <div class="flex flex-wrap gap-1 mb-2">
                    <span class="text-xs bg-gray-100 px-2 py-0.5 rounded">
                        <i class="fas fa-tag mr-1"></i>{{ $item->category->name }}
                    </span>
                    <span class="text-xs bg-gray-100 px-2 py-0.5 rounded">
                        <i class="fas fa-building mr-1"></i>{{ $item->unit->name }}
                    </span>
                </div>
                
                <!-- Info -->
                <p class="text-gray-600 text-sm mb-1"><i class="fas fa-barcode mr-2"></i>{{ $item->code }}</p>
                <p class="text-gray-600 text-sm mb-1">
                    <i class="fas fa-boxes mr-2"></i>Stok: <span class="font-semibold {{ $item->stock > 0 ? 'text-green-600' : 'text-red-500' }}">{{ $item->stock }}</span>
                </p>
                <p class="text-gray-600 text-sm mb-3"><i class="fas fa-map-marker-alt mr-2"></i>{{ $item->location }}</p>
                
                <!-- Alasan Tidak Tersedia -->
                @if(!$canBorrow)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-2 mb-3 text-xs">
                        @if($isBorrowed && $activeCirculation)
                            <p class="text-red-600">
                                <i class="fas fa-user mr-1"></i> Dipinjam: <strong>{{ $activeCirculation->borrower_name }}</strong>
                            </p>
                            <p class="text-red-500">
                                <i class="fas fa-calendar-alt mr-1"></i> Kembali: {{ $activeCirculation->expected_return_date->format('d/m/Y') }}
                            </p>
                        @elseif($isStockEmpty && $item->status == 'available')
                            <p class="text-yellow-700">
                                <i class="fas fa-exclamation-triangle mr-1"></i> <strong>Stok Habis</strong>
                            </p>
                        @elseif($item->condition == 'rusak')
                            <p class="text-yellow-700"><i class="fas fa-exclamation-triangle mr-1"></i> <strong>Barang Rusak</strong></p>
                        @elseif($item->condition == 'perbaikan')
                            <p class="text-orange-700"><i class="fas fa-tools mr-1"></i> <strong>Dalam Perbaikan</strong></p>
                        @endif
                    </div>
                @endif
                
                <!-- Aksi -->
                <div class="flex gap-2 mt-2">
                    <a href="{{ route('user.items.show', $item) }}" 
                       class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-center px-3 py-1.5 rounded transition text-sm">
                        <i class="fas fa-eye mr-1"></i>Detail
                    </a>
                    @if($canBorrow)
                        <a href="{{ route('user.circulations.create') }}?item={{ $item->id }}" 
                           class="flex-1 bg-green-500 hover:bg-green-600 text-white text-center px-3 py-1.5 rounded transition text-sm">
                            <i class="fas fa-hand-paper mr-1"></i>Pinjam
                        </a>
                    @else
                        <button disabled class="flex-1 bg-gray-300 text-gray-500 text-center px-3 py-1.5 rounded cursor-not-allowed text-sm">
                            <i class="fas fa-times mr-1"></i>
                            @if($isStockEmpty) Stok Habis
                            @elseif($isBorrowed) Dipinjam
                            @elseif($isBroken) Rusak
                            @else Tidak Tersedia
                            @endif
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 bg-gray-50 rounded-lg">
            <i class="fas fa-box-open text-6xl text-gray-300 mb-4 block"></i>
            <p class="text-gray-500 text-lg">Tidak ada barang ditemukan</p>
            <p class="text-gray-400 text-sm">Coba ubah filter atau kata kunci pencarian</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $items->withQueryString()->links() }}
    </div>
</div>
@endsection