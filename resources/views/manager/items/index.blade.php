@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Daftar Barang - {{ auth()->user()->unit->name }}</h1>
    </div>

    <div class="mb-6">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Cari barang..." 
                   class="flex-1 px-4 py-2 border rounded-lg">
            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($items as $item)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-48 object-cover">
            <div class="p-4">
                <h3 class="font-bold text-lg">{{ $item->name }}</h3>
                <p class="text-gray-600 text-sm"><i class="fas fa-barcode mr-2"></i>{{ $item->code }}</p>
                <p class="text-gray-600 text-sm"><i class="fas fa-map-marker-alt mr-2"></i>{{ $item->location }}</p>
                <p class="text-gray-600 text-sm"><i class="fas fa-tag mr-2"></i>{{ $item->category->name }}</p>
                <div class="mt-3">
                    <span class="px-2 py-1 text-xs rounded-full
                        {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $item->status == 'available' ? 'Tersedia' : 'Dipinjam' }}
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $items->links() }}
    </div>
</div>
@endsection