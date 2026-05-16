@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Manajemen Barang</h1>
        <a href="{{ route('admin.items.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Tambah Barang
        </a>
    </div>

    <!-- Search -->
    <div class="mb-6">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Cari kode, nama, atau lokasi..." 
                   class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
            <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
            @if(request('search'))
                <a href="{{ route('admin.items.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Cards View (Mobile) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        @forelse($items as $item)
        <div class="bg-white rounded-lg shadow-md overflow-hidden card-hover">
            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-48 object-cover">
            <div class="p-4">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-bold text-lg">{{ $item->name }}</h3>
                    <span class="px-2 py-1 text-xs rounded-full 
                        {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : 
                           ($item->status == 'borrowed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ $item->status == 'available' ? 'Tersedia' : ($item->status == 'borrowed' ? 'Dipinjam' : 'Perbaikan') }}
                    </span>
                </div>
                <p class="text-gray-600 text-sm mb-1"><i class="fas fa-barcode mr-2"></i>{{ $item->code }}</p>
                <p class="text-gray-600 text-sm mb-1"><i class="fas fa-map-marker-alt mr-2"></i>{{ $item->location }}</p>
                <p class="text-gray-600 text-sm mb-3"><i class="fas fa-tag mr-2"></i>{{ $item->category->name }}</p>
                <div class="flex gap-2">
                    <a href="{{ route('admin.items.show', $item) }}" class="flex-1 bg-blue-500 text-white text-center px-3 py-1 rounded hover:bg-blue-600">
                        <i class="fas fa-eye mr-1"></i>Detail
                    </a>
                    <a href="{{ route('admin.items.edit', $item) }}" class="flex-1 bg-yellow-500 text-white text-center px-3 py-1 rounded hover:bg-yellow-600">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                    <form action="{{ route('admin.items.destroy', $item) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600" onclick="return confirm('Yakin ingin menghapus?')">
                            <i class="fas fa-trash mr-1"></i>Hapus
                        </button>
                    </form>
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

    <!-- Table View (Desktop) -->
    <div class="bg-white rounded-lg shadow overflow-hidden hidden md:block">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($items as $item)
                <tr>
                    <td class="px-6 py-4">{{ $item->code }}</td>
                    <td class="px-6 py-4 font-medium">{{ $item->name }}</td>
                    <td class="px-6 py-4">{{ $item->category->name }}</td>
                    <td class="px-6 py-4">{{ $item->location }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : 
                               ($item->status == 'borrowed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ $item->status == 'available' ? 'Tersedia' : ($item->status == 'borrowed' ? 'Dipinjam' : 'Perbaikan') }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.items.show', $item) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.items.edit', $item) }}" class="text-yellow-600 hover:text-yellow-800">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.items.destroy', $item) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Yakin?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $items->withQueryString()->links() }}
    </div>
</div>
@endsection