@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-2xl px-4 sm:px-6 py-6">
    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                <i class="fas fa-trash-can text-red-500 mr-2"></i>Ajukan Penghapusan
            </h1>
            <p class="text-sm text-gray-500 mt-1">Pilih kode stok spesifik barang yang rusak</p>
        </div>
        <a href="{{ route('user.disposals.index') }}" 
           class="text-gray-600 hover:text-gray-800 text-sm inline-flex items-center gap-1">
            <i class="fas fa-arrow-left"></i>
            <span>Riwayat Pengajuan</span>
        </a>
    </div>

    {{-- INFO BOX --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center shrink-0">
                <i class="fas fa-info-circle text-blue-500"></i>
            </div>
            <div class="text-xs sm:text-sm text-blue-700 leading-relaxed">
                <p class="font-semibold mb-1">Info Penting</p>
                <p>Setiap unit stok punya <strong>kode unik</strong> (misal <span class="font-mono">ELC/LAP-003.LEM.TKT/3.01/22/IX/2026</span>). Pilih kode stok yang <strong>rusak spesifik</strong>, bukan cuma nama barangnya.</p>
            </div>
        </div>
    </div>

    {{-- FORM --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-7">
        <form action="{{ route('user.disposals.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-5">

                {{-- ============================================================ --}}
                {{-- 1. PILIH BARANG                                              --}}
                {{-- ============================================================ --}}
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">
                        <i class="fas fa-boxes-stacked text-brand-500 mr-1.5"></i>
                        Pilih Barang <span class="text-red-500">*</span>
                    </label>
                    <select name="item_id" id="itemSelect" required
                            class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($items as $item)
                        <option value="{{ $item->id }}"
                                data-stock="{{ $item->stock }}"
                                {{ old('item_id', $selectedItem?->id) == $item->id ? 'selected' : '' }}>
                            {{ $item->name }} — Stok: {{ $item->stock }}
                        </option>
                        @endforeach
                    </select>
                    @error('item_id') 
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>{{ $message }}
                        </p> 
                    @enderror
                </div>

                {{-- ============================================================ --}}
                {{-- 2. PILIH KODE STOK SPESIFIK                                  --}}
                {{-- ============================================================ --}}
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">
                        <i class="fas fa-qrcode text-brand-500 mr-1.5"></i>
                        Pilih Kode Stok yang Rusak <span class="text-red-500">*</span>
                    </label>

                    {{-- Loading --}}
                    <div id="stockLoading" class="hidden text-center py-6 border-2 border-gray-200 rounded-xl bg-gray-50">
                        <i class="fas fa-spinner fa-spin text-xl text-brand-500"></i>
                        <p class="text-xs text-gray-500 mt-2">Memuat kode stok...</p>
                    </div>

                    {{-- Placeholder --}}
                    <div id="stockPlaceholder" class="text-center py-6 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                        <i class="fas fa-info-circle text-2xl text-gray-300 mb-2 block"></i>
                        <p class="text-xs text-gray-500">Pilih barang dulu untuk menampilkan kode stok</p>
                    </div>

                    {{-- List Kode Stok --}}
                    <div id="stockListWrapper" class="hidden">
                        <div class="border-2 border-gray-200 rounded-xl p-3 max-h-72 overflow-y-auto bg-gray-50 space-y-2">
                            <div id="stockList"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Pilih 1 kode stok yang rusak. Kode lain tetap aman.
                        </p>
                    </div>

                    {{-- Hidden Input --}}
                    <input type="hidden" name="item_stock_id" id="selectedStockId" value="{{ old('item_stock_id') }}">

                    @error('item_stock_id') 
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>{{ $message }}
                        </p> 
                    @enderror
                </div>

                {{-- ============================================================ --}}
                {{-- 3. ALASAN                                                    --}}
                {{-- ============================================================ --}}
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">
                        <i class="fas fa-clipboard-list text-brand-500 mr-1.5"></i>
                        Alasan Kerusakan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="reason" rows="4" required minlength="10" maxlength="500"
                              class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition resize-none"
                              placeholder="Jelaskan kondisi kerusakan detail, misal: layar pecah, keyboard tidak berfungsi, dll.">{{ old('reason') }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Minimal 10 karakter, maksimal 500 karakter
                    </p>
                    @error('reason') 
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>{{ $message }}
                        </p> 
                    @enderror
                </div>

                {{-- ============================================================ --}}
                {{-- 4. FOTO BUKTI                                                --}}
                {{-- ============================================================ --}}
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">
                        <i class="fas fa-camera text-brand-500 mr-1.5"></i>
                        Foto Bukti Kerusakan <span class="text-red-500">*</span>
                    </label>

                    <label for="photosInput" 
                           class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-brand-50 hover:border-brand-400 transition">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                            <p class="text-xs text-gray-500 font-medium">Klik untuk upload foto</p>
                            <p class="text-[10px] text-gray-400 mt-1">JPG/PNG, max 2MB per foto</p>
                        </div>
                        <input id="photosInput" name="photos[]" type="file" class="hidden" accept="image/*" multiple required>
                    </label>

                    {{-- Preview --}}
                    <div id="photoPreview" class="grid grid-cols-3 sm:grid-cols-4 gap-2 mt-3 hidden"></div>

                    @error('photos') 
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>{{ $message }}
                        </p> 
                    @enderror
                    @error('photos.*') 
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>{{ $message }}
                        </p> 
                    @enderror
                </div>
            </div>

            {{-- TOMBOL AKSI --}}
            <div class="flex flex-col sm:flex-row gap-2 justify-end mt-7 pt-5 border-t border-gray-100">
                <a href="{{ route('user.disposals.index') }}" 
                   class="px-5 py-2.5 border-2 border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 text-sm font-medium text-center inline-flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i>
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl text-sm font-medium shadow-sm hover:shadow-md transition inline-flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const itemSelect       = document.getElementById('itemSelect');
    const stockLoading     = document.getElementById('stockLoading');
    const stockPlaceholder = document.getElementById('stockPlaceholder');
    const stockListWrapper = document.getElementById('stockListWrapper');
    const stockList        = document.getElementById('stockList');
    const selectedStockId  = document.getElementById('selectedStockId');

    // Simpan old value dari session (kalau validasi gagal)
    const oldStockId = selectedStockId.value;

    function loadStockCodes(itemId) {
        // Reset
        stockList.innerHTML = '';
        selectedStockId.value = '';

        if (!itemId) {
            stockPlaceholder.classList.remove('hidden');
            stockListWrapper.classList.add('hidden');
            return;
        }

        // Show loading
        stockPlaceholder.classList.add('hidden');
        stockListWrapper.classList.add('hidden');
        stockLoading.classList.remove('hidden');

        // Fetch kode stok
        fetch(`{{ url('user/items') }}/${itemId}/stock-codes`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            stockLoading.classList.add('hidden');

            if (!data.stockCodes || data.stockCodes.length === 0) {
                stockPlaceholder.classList.remove('hidden');
                stockPlaceholder.innerHTML = `
                    <i class="fas fa-exclamation-circle text-2xl text-yellow-400 mb-2 block"></i>
                    <p class="text-xs text-gray-500">Tidak ada kode stok tersedia untuk barang ini</p>
                `;
                return;
            }

            stockListWrapper.classList.remove('hidden');

            data.stockCodes.forEach((sc, index) => {
                const label = document.createElement('label');
                label.className = 'flex items-center gap-3 p-3 bg-white rounded-xl border-2 border-gray-200 hover:border-brand-400 hover:bg-brand-50 cursor-pointer transition';
                label.style.animationDelay = (index * 0.03) + 's';

                const isChecked = oldStockId && oldStockId == sc.id;

                label.innerHTML = `
                    <input type="radio" name="stock_radio" value="${sc.id}"
                           class="w-4 h-4 text-brand-500 focus:ring-brand-500 shrink-0 cursor-pointer"
                           ${isChecked ? 'checked' : ''}>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-md text-[10px] font-bold shrink-0"
                                  style="background: #0F6B5F20; color: #0F6B5F;">
                                ${String(sc.no).padStart(2, '0')}
                            </span>
                            <span class="text-[10px] font-semibold text-gray-500 uppercase">Stok ke-${sc.no}</span>
                        </div>
                        <p class="font-mono text-[11px] font-bold break-all leading-tight"
                           style="color: #0F6B5F;">
                            ${sc.code}
                        </p>
                    </div>
                    <i class="fas fa-check-circle text-brand-500 text-lg hidden check-icon"></i>
                `;

                // Kalau old value, tambah highlight
                if (isChecked) {
                    label.classList.add('border-brand-500', 'bg-brand-50');
                    label.querySelector('.check-icon')?.classList.remove('hidden');
                }

                // Event saat dipilih
                const radio = label.querySelector('input[type="radio"]');
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        selectedStockId.value = this.value;

                        // Reset semua label
                        stockList.querySelectorAll('label').forEach(l => {
                            l.classList.remove('border-brand-500', 'bg-brand-50');
                            l.querySelector('.check-icon')?.classList.add('hidden');
                        });

                        // Highlight label yang dipilih
                        label.classList.add('border-brand-500', 'bg-brand-50');
                        label.querySelector('.check-icon')?.classList.remove('hidden');
                    }
                });

                stockList.appendChild(label);
            });
        })
        .catch(err => {
            console.error('Error loading stock codes:', err);
            stockLoading.classList.add('hidden');
            stockPlaceholder.classList.remove('hidden');
            stockPlaceholder.innerHTML = `
                <i class="fas fa-exclamation-circle text-2xl text-red-400 mb-2 block"></i>
                <p class="text-xs text-red-500">Gagal memuat kode stok</p>
            `;
        });
    }

    // Event saat barang dipilih
    itemSelect.addEventListener('change', function () {
        loadStockCodes(this.value);
    });

    // Trigger saat page load (kalau barang sudah terpilih)
    if (itemSelect.value) {
        loadStockCodes(itemSelect.value);
    }

    // ============================================================
    // Preview foto
    // ============================================================
    const photosInput = document.getElementById('photosInput');
    const photoPreview = document.getElementById('photoPreview');

    if (photosInput) {
        photosInput.addEventListener('change', function () {
            photoPreview.innerHTML = '';
            const files = Array.from(this.files);

            if (files.length === 0) {
                photoPreview.classList.add('hidden');
                return;
            }

            photoPreview.classList.remove('hidden');

            files.forEach((file, i) => {
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = 'relative aspect-square rounded-lg overflow-hidden border-2 border-gray-200';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                        <div class="absolute top-1 right-1 bg-black/60 text-white text-[9px] font-bold px-1.5 py-0.5 rounded">
                            ${i + 1}
                        </div>
                    `;
                    photoPreview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
    }
});
</script>
@endpush