@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-2xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Detail Kategori</h1>
        <div>
            <a href="{{ route('admin.categories.edit', $category) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('admin.categories.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 ml-2">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-500">Kode Kategori</label>
                        <p class="font-medium text-lg">{{ $category->code }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Nama Kategori</label>
                        <p class="font-medium text-lg">{{ $category->name }}</p>
                    </div>
                    <div class="col-span-2">
                        <label class="text-sm text-gray-500">Deskripsi</label>
                        <p class="mt-1">{{ $category->description ?: '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Total Barang</label>
                        <p class="font-medium">{{ $category->items()->count() }} barang</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Dibuat</label>
                        <p>{{ $category->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Barang dalam Kategori -->
    <div class="mt-6 bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-lg">Daftar Barang dalam Kategori Ini</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Kode</th>
                        <th class="px-4 py-2 text-left">Nama Barang</th>
                        <th class="px-4 py-2 text-left">Unit</th>
                        <th class="px-4 py-2 text-left">Lokasi</th>
                        <th class="px-4 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($category->items as $item)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $item->code }}</td>
                        <td class="px-4 py-2">{{ $item->name }}</td>
                        <td class="px-4 py-2">{{ $item->unit->name }}</td>
                        <td class="px-4 py-2">{{ $item->location }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $item->status == 'available' ? 'Tersedia' : 'Dipinjam' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            Belum ada barang dalam kategori ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection