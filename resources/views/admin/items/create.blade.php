@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-3xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Tambah Barang</h1>
        <a href="{{ route('super_admin.items.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('super_admin.items.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">Nama Barang *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Jenis/Kategori *</label>
                    <select name="category_id" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Unit *</label>
                    <select name="unit_id" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Pilih Unit</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('unit_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Tanggal Pembelian</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date') }}"
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Kondisi *</label>
                    <select name="condition" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="baik" {{ old('condition') == 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="rusak" {{ old('condition') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                        <option value="perbaikan" {{ old('condition') == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Harga (Opsional)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                        <input type="number" name="price" value="{{ old('price') }}" step="0.01"
                               class="w-full pl-10 pr-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Lokasi</label>
                    <input type="text" name="location" value="{{ old('location') }}"
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                           placeholder="Contoh: Rak A, Lemari 1">
                </div>
<!-- Tambahkan di dalam form, setelah Lokasi -->
<div>
    <label class="block text-sm font-medium mb-2">Gambar Barang</label>
    <input type="file" name="image" accept="image/*"
           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, JPEG. Maks: 2MB</p>
    @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">Deskripsi</label>
                    <textarea name="description" rows="3" 
                              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm text-blue-700 font-medium">Sistem akan otomatis:</p>
                        <ul class="text-sm text-blue-600 mt-1 list-disc list-inside">
                            <li>Membuat kode barang unik (format: INV-UNIT-0001)</li>
                            <li>Membuat QR Code untuk barang ini</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="reset" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Reset</button>
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection