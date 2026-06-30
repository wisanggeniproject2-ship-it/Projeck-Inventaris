@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-2xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Edit Barang</h1>
        <a href="{{ route('super_admin.items.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('super_admin.items.update', $item) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Barang -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">Nama Barang *</label>
                    <input type="text" name="name" value="{{ old('name', $item->name) }}" required
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-medium mb-2">Kategori *</label>
                    <select name="category_id" required class="w-full px-3 py-2 border rounded-lg">
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Unit -->
                <div>
                    <label class="block text-sm font-medium mb-2">Unit *</label>
                    <select name="unit_id" required class="w-full px-3 py-2 border rounded-lg">
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id', $item->unit_id) == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tanggal Pembelian -->
                <div>
                    <label class="block text-sm font-medium mb-2">Tanggal Pembelian</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', $item->purchase_date ? $item->purchase_date->format('Y-m-d') : '') }}"
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <!-- Kondisi -->
                <div>
                    <label class="block text-sm font-medium mb-2">Kondisi *</label>
                    <select name="condition" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="baik" {{ old('condition', $item->condition) == 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="rusak" {{ old('condition', $item->condition) == 'rusak' ? 'selected' : '' }}>Rusak</option>
                        <option value="perbaikan" {{ old('condition', $item->condition) == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                    </select>
                </div>

                <!-- Harga -->
                <div>
                    <label class="block text-sm font-medium mb-2">Harga</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                        <input type="number" name="price" value="{{ old('price', $item->price) }}" step="0.01"
                               class="w-full pl-10 pr-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <!-- Lokasi -->
                <div>
                    <label class="block text-sm font-medium mb-2">Lokasi</label>
                    <input type="text" name="location" value="{{ old('location', $item->location) }}"
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                           placeholder="Contoh: Rak A, Lemari 1">
                </div>
<!-- Tambahkan di dalam form, setelah Lokasi -->
<div>
    <label class="block text-sm font-medium mb-2">Gambar Barang</label>
    @if($item->image)
    <div class="mb-2">
        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="h-32 w-auto rounded-lg object-cover">
    </div>
    @endif
    <input type="file" name="image" accept="image/*"
           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah gambar. Format: JPG, PNG, JPEG. Maks: 2MB</p>
    @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>
                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border rounded-lg">
                        <option value="available" {{ old('status', $item->status) == 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="borrowed" {{ old('status', $item->status) == 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="maintenance" {{ old('status', $item->status) == 'maintenance' ? 'selected' : '' }}>Perbaikan</option>
                    </select>
                </div>

                <!-- Deskripsi -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">Deskripsi</label>
                    <textarea name="description" rows="3" 
                              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">{{ old('description', $item->description) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <a href="{{ route('super_admin.items.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    <i class="fas fa-save mr-2"></i>Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection