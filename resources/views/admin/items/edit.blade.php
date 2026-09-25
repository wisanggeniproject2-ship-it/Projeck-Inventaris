@extends('layouts.app')

@section('content')
@php
    $prefix = auth()->user()->role === 'super_admin' ? 'super_admin' : 'admin_unit';
    
    // 🔥 Info stok — EXCLUDE disposed biar sesuai fisik yang berlaku
    $totalFisik       = $item->stockCodes()->where('status', '!=', 'disposed')->count();
    $availableStock   = $item->stockCodes()->where('status', 'available')->count();
    $borrowedStock    = $item->stockCodes()->where('status', 'borrowed')->count();
    $disposedStock    = $item->stockCodes()->where('status', 'disposed')->count();
    $maintenanceStock = $item->stockCodes()->where('status', 'maintenance')->count();
@endphp

<div class="container mx-auto max-w-4xl px-3 sm:px-4 lg:px-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-5 sm:mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                <i class="fas fa-edit text-brand-500 mr-2"></i>Edit Barang
            </h1>
            <p class="text-sm text-gray-500 mt-1">Ubah data barang: {{ $item->name }}</p>
        </div>
        <a href="{{ route($prefix . '.items.index') }}" 
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-brand-600 transition">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Daftar
        </a>
    </div>

    {{-- ALERT ERROR --}}
    @if(session('error'))
        <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl">
            <p class="text-sm font-semibold text-red-700 mb-2">
                <i class="fas fa-exclamation-triangle mr-1"></i>Ada kesalahan:
            </p>
            <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 🔥 INFO STOK SAAT INI --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-2xl p-4 sm:p-5 mb-5">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center shrink-0">
                <i class="fas fa-info-circle text-blue-500"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-blue-700 uppercase tracking-wide mb-2">
                    Info Stok Saat Ini
                </p>
                <div class="grid grid-cols-3 gap-2">
                    {{-- Total (exclude disposed) --}}
                    <div class="bg-white rounded-lg p-2 border border-gray-200">
                        <p class="text-[10px] text-gray-500 uppercase">Total Fisik</p>
                        <p class="text-lg font-bold text-gray-700">{{ $totalFisik }}</p>
                    </div>
                    {{-- Tersedia --}}
                    <div class="bg-green-50 rounded-lg p-2 border border-green-200">
                        <p class="text-[10px] text-green-700 uppercase">Tersedia</p>
                        <p class="text-lg font-bold text-green-700">{{ $availableStock }}</p>
                    </div>
                    {{-- Dipinjam --}}
                    <div class="bg-orange-50 rounded-lg p-2 border border-orange-200">
                        <p class="text-[10px] text-orange-700 uppercase">Dipinjam</p>
                        <p class="text-lg font-bold text-orange-700">{{ $borrowedStock }}</p>
                    </div>
                </div>

                {{-- Info disposed (kalau ada) --}}
                @if($disposedStock > 0)
                    <p class="text-xs text-red-700 mt-3 leading-relaxed">
                        <i class="fas fa-trash-can mr-1"></i>
                        <strong>{{ $disposedStock }} unit sudah dihapus (disposed).</strong>
                        Tidak dihitung dalam total fisik.
                    </p>
                @endif

                @if($borrowedStock > 0)
                    <p class="text-xs text-blue-700 mt-2 leading-relaxed">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>Ada {{ $borrowedStock }} unit sedang dipinjam.</strong>
                        Stok total minimal harus <strong>{{ $borrowedStock }}</strong> (tidak bisa dikurangi di bawah jumlah yang dipinjam).
                    </p>
                @else
                    <p class="text-xs text-blue-700 mt-3 leading-relaxed">
                        <i class="fas fa-check-circle mr-1"></i>
                        Semua unit tersedia. Bebas ubah stok total sesuai kebutuhan.
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- FORM --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-7">
        <form action="{{ route($prefix . '.items.update', $item) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-5">

                {{-- Nama Barang --}}
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">
                        <i class="fas fa-tag text-brand-500 mr-1.5"></i>
                        Nama Barang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $item->name) }}" 
                           required
                           maxlength="200"
                           class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition">
                </div>

                {{-- Kategori & Unit --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Kategori --}}
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">
                            <i class="fas fa-list text-brand-500 mr-1.5"></i>
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select name="category_id" required
                                class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Unit --}}
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">
                            <i class="fas fa-building text-brand-500 mr-1.5"></i>
                            Unit <span class="text-red-500">*</span>
                        </label>
                        <select name="unit_id" required
                                class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition">
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id', $item->unit_id) == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- 🔥 STOK TOTAL — EDIT DI SINI --}}
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">
                        <i class="fas fa-boxes text-brand-500 mr-1.5"></i>
                        Stok Total <span class="text-red-500">*</span>
                    </label>

                    {{-- Info hint --}}
                    <div class="mb-2 flex items-start gap-2 text-xs text-gray-500">
                        <i class="fas fa-info-circle mt-0.5 text-blue-500"></i>
                        <p>
                            Isi jumlah <strong>fisik yang berlaku</strong> (tidak termasuk yang sudah disposed).
                            @if($borrowedStock > 0)
                                <br><span class="text-orange-600">Minimal: <strong>{{ $borrowedStock }}</strong> (karena ada {{ $borrowedStock }} unit yang sedang dipinjam).</span>
                            @endif
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <i class="fas fa-cubes absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="number" 
                                   name="stock" 
                                   id="stockInput"
                                   value="{{ old('stock', $totalFisik) }}"
                                   required
                                   min="{{ $borrowedStock > 0 ? $borrowedStock : 1 }}"
                                   class="w-full pl-10 pr-3 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition font-bold text-lg">
                        </div>

                        {{-- Info live --}}
                        <div class="shrink-0 px-3 py-2 rounded-xl bg-gray-100 border-2 border-gray-200 text-center">
                            <p class="text-[10px] text-gray-500 uppercase">Total Lama</p>
                            <p class="text-lg font-bold text-gray-700">{{ $totalFisik }}</p>
                        </div>
                    </div>

                    {{-- Preview perubahan --}}
                    <div id="previewBox" class="mt-3 hidden">
                        <div class="p-3 rounded-xl bg-blue-50 border border-blue-200 text-xs text-blue-700 flex items-start gap-2">
                            <i class="fas fa-eye mt-0.5"></i>
                            <div id="previewText">
                                <!-- JS akan isi di sini -->
                            </div>
                        </div>
                    </div>

                    @error('stock')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Kondisi & Tanggal Beli --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Kondisi --}}
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">
                            <i class="fas fa-heartbeat text-brand-500 mr-1.5"></i>
                            Kondisi <span class="text-red-500">*</span>
                        </label>
                        <select name="condition" required
                                class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition">
                            <option value="baik" {{ old('condition', $item->condition) == 'baik' ? 'selected' : '' }}>✅ Baik</option>
                            <option value="rusak" {{ old('condition', $item->condition) == 'rusak' ? 'selected' : '' }}>⚠️ Rusak</option>
                            <option value="perbaikan" {{ old('condition', $item->condition) == 'perbaikan' ? 'selected' : '' }}>🔧 Perbaikan</option>
                        </select>
                    </div>

                    {{-- Tanggal Beli --}}
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">
                            <i class="fas fa-calendar text-brand-500 mr-1.5"></i>
                            Tanggal Beli
                        </label>
                        <input type="date" 
                               name="purchase_date" 
                               value="{{ old('purchase_date', $item->purchase_date?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition">
                    </div>
                </div>

                {{-- Harga & Sumber Dana --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Harga --}}
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">
                            <i class="fas fa-money-bill-wave text-brand-500 mr-1.5"></i>
                            Harga
                        </label>
                        <input type="number" 
                               name="price" 
                               value="{{ old('price', $item->price) }}"
                               min="0"
                               step="0.01"
                               class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition">
                    </div>

                    {{-- Sumber Dana --}}
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">
                            <i class="fas fa-hand-holding-dollar text-brand-500 mr-1.5"></i>
                            Sumber Dana
                        </label>
                        <select name="funding_source_id"
                                class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition">
                            <option value="">-- Pilih Sumber Dana --</option>
                            @foreach($fundingSources as $fs)
                                <option value="{{ $fs->id }}" {{ old('funding_source_id', $item->funding_source_id) == $fs->id ? 'selected' : '' }}>
                                    {{ $fs->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Lokasi --}}
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">
                        <i class="fas fa-map-marker-alt text-brand-500 mr-1.5"></i>
                        Lokasi
                    </label>
                    <input type="text" 
                           name="location" 
                           value="{{ old('location', $item->location) }}"
                           maxlength="200"
                           class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition">
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">
                        <i class="fas fa-align-left text-brand-500 mr-1.5"></i>
                        Deskripsi
                    </label>
                    <textarea name="description" 
                              rows="3"
                              class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition resize-none">{{ old('description', $item->description) }}</textarea>
                </div>

                {{-- Gambar --}}
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">
                        <i class="fas fa-image text-brand-500 mr-1.5"></i>
                        Gambar Barang
                    </label>

                    @if($item->image)
                        <div class="mb-3 flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200">
                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                                 class="w-20 h-20 rounded-lg object-cover border border-gray-200">
                            <div class="text-xs text-gray-500">
                                <p class="font-medium">Gambar saat ini</p>
                                <p>Upload baru untuk mengganti</p>
                            </div>
                        </div>
                    @endif

                    <input type="file" 
                           name="image" 
                           accept="image/jpeg,image/png,image/jpg"
                           class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Format: JPG, PNG. Maks 2 MB.
                    </p>
                </div>

            </div>

            {{-- TOMBOL AKSI --}}
            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-7 pt-5 border-t border-gray-100">
                <a href="{{ route($prefix . '.items.index') }}" 
                   class="px-5 py-2.5 border-2 border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 transition text-sm font-medium text-center inline-flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i>
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-white rounded-xl transition-all text-sm font-medium shadow-sm hover:shadow-md inline-flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ============================================================
    // 🔥 LIVE PREVIEW — Perubahan stok
    // ============================================================
    document.addEventListener('DOMContentLoaded', function () {
        const stockInput  = document.getElementById('stockInput');
        const previewBox  = document.getElementById('previewBox');
        const previewText = document.getElementById('previewText');

        const totalFisik     = {{ $totalFisik }};
        const availableStock = {{ $availableStock }};
        const borrowedStock  = {{ $borrowedStock }};

        function updatePreview() {
            const newStock = parseInt(stockInput.value) || 0;
            const diff = newStock - totalFisik;

            if (diff === 0) {
                previewBox.classList.add('hidden');
                return;
            }

            previewBox.classList.remove('hidden');
            previewBox.querySelector('div').className = 'p-3 rounded-xl text-xs flex items-start gap-2 ' +
                (diff > 0 
                    ? 'bg-green-50 border border-green-200 text-green-700'
                    : 'bg-orange-50 border border-orange-200 text-orange-700');

            let message = '';

            if (diff > 0) {
                message = `<strong>Stok akan ditambah ${diff} unit.</strong><br>` +
                          `Sistem akan otomatis membuat <strong>${diff} kode stok baru</strong> ` +
                          `(dengan nomor lanjut dari yang terakhir).`;
            } else {
                const removeCount = Math.abs(diff);
                if (removeCount > availableStock) {
                    message = `<strong>⚠️ Tidak bisa kurangi ${removeCount} unit!</strong><br>` +
                              `Hanya ada <strong>${availableStock}</strong> unit yang tersedia. ` +
                              `Sisa <strong>${borrowedStock}</strong> unit sedang dipinjam, tidak bisa dihapus.`;
                    previewBox.querySelector('div').className = 'p-3 rounded-xl text-xs flex items-start gap-2 bg-red-50 border border-red-200 text-red-700';
                } else {
                    message = `<strong>Stok akan dikurangi ${removeCount} unit.</strong><br>` +
                              `Sistem akan hapus <strong>${removeCount} kode stok</strong> dengan nomor terbesar ` +
                              `yang statusnya <span class="text-green-700 font-semibold">available</span>.`;
                }
            }

            previewText.innerHTML = message;
        }

        if (stockInput) {
            stockInput.addEventListener('input', updatePreview);
            updatePreview();
        }
    });
</script>
@endpush