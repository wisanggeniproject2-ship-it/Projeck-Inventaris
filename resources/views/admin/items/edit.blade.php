@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-2xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Edit Barang</h1>
        <a href="{{ route('admin.items.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.items.update', $item) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-2">Kode Barang *</label>
                    <input type="text" name="code" value="{{ old('code', $item->code) }}" required
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Nama Barang *</label>
                    <input type="text" name="name" value="{{ old('name', $item->name) }}" required
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>

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

                <div>
                    <label class="block text-sm font-medium mb-2">Lokasi *</label>
                    <input type="text" name="location" value="{{ old('location', $item->location) }}" required
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Sumber Anggaran</label>
                    <input type="text" name="budget_source" value="{{ old('budget_source', $item->budget_source) }}"
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border rounded-lg">
                        <option value="available" {{ $item->status == 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="borrowed" {{ $item->status == 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="maintenance" {{ $item->status == 'maintenance' ? 'selected' : '' }}>Perbaikan</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">Gambar</label>
                    @if($item->image)
                    <div class="mb-2">
                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="h-32 w-auto">
                    </div>
                    @endif
                    <input type="file" name="image" accept="image/*" class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border rounded-lg">{{ old('description', $item->description) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    <i class="fas fa-save mr-2"></i>Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection