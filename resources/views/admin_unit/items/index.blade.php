@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Daftar Barang - {{ auth()->user()->unit->name }}</h1>
        <a href="{{ route('admin_unit.items.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Tambah Barang
        </a>
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
            @if(request('search'))
            <a href="{{ route('admin_unit.items.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                Reset
            </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kondisi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($items as $item)
                <tr>
                    <td class="px-6 py-4 font-mono text-sm">{{ $item->code }}</td>
                    <td class="px-6 py-4">{{ $item->name }}</td>
                    <td class="px-6 py-4">{{ $item->category->name }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $item->condition == 'baik' ? 'bg-green-100 text-green-700' : 
                               ($item->condition == 'rusak' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst($item->condition) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : 
                               ($item->status == 'borrowed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ $item->status == 'available' ? 'Tersedia' : ($item->status == 'borrowed' ? 'Dipinjam' : 'Perbaikan') }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin_unit.items.show', $item) }}" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-box-open text-4xl mb-2 block"></i>
                        Belum ada data barang di unit ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $items->links() }}
    </div>
</div>
@endsection