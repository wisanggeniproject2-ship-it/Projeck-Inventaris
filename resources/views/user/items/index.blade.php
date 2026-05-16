@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Daftar Barang - {{ auth()->user()->unit->name }}</h1>
    </div>

    <!-- Search -->
    <div class="mb-6">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Cari kode, nama, atau lokasi..." 
                   class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
        </form>
    </div>

    <!-- Cards View -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($items as $item)
        <div class="bg-white rounded-lg shadow-md overflow-hidden card-hover">
            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-48 object-cover">
            <div class="p-4">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-bold text-lg">{{ $item->name }}</h3>
                    <span class="px-2 py-1 text-xs rounded-full 
                        {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $item->status == 'available' ? 'Tersedia' : 'Dipinjam' }}
                    </span>
                </div>
                <p class="text-gray-600 text-sm mb-1"><i class="fas fa-barcode mr-2"></i>{{ $item->code }}</p>
                <p class="text-gray-600 text-sm mb-1"><i class="fas fa-map-marker-alt mr-2"></i>{{ $item->location }}</p>
                <p class="text-gray-600 text-sm mb-3"><i class="fas fa-tag mr-2"></i>{{ $item->category->name }}</p>
                
                <div class="flex gap-2">
                    <a href="{{ route('user.items.show', $item) }}" class="flex-1 bg-blue-500 text-white text-center px-3 py-1 rounded hover:bg-blue-600">
                        <i class="fas fa-eye mr-1"></i>Detail
                    </a>
                    @if($item->status == 'available')
                    <a href="{{ route('user.circulations.create') }}?item={{ $item->id }}" class="flex-1 bg-green-500 text-white text-center px-3 py-1 rounded hover:bg-green-600">
                        <i class="fas fa-hand-paper mr-1"></i>Pinjam
                    </a>
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
        {{ $items->links() }}
    </div>
</div>
@endsection