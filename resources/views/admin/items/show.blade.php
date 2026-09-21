@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-4xl px-2 sm:px-4">
    <div class="flex flex-wrap justify-between items-center gap-3 mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Detail Barang</h1>
        <div class="flex gap-2 flex-wrap">
            <!-- 🔥 TOMBOL CETAK PDF -->
            <a href="{{ route('super_admin.items.pdf', $item) }}" 
               target="_blank"
               class="bg-red-500 hover:bg-red-600 hover:shadow-md active:scale-[0.98] text-white px-4 py-2 rounded-lg transition-all text-sm inline-flex items-center">
                <i class="fas fa-file-pdf mr-2"></i>Cetak PDF
            </a>

            <!-- 🔥 TOMBOL EXPORT PNG -->
            <a href="{{ route('super_admin.items.png', $item) }}" 
               download
               class="bg-teal-600 hover:bg-teal-700 hover:shadow-md active:scale-[0.98] text-white px-4 py-2 rounded-lg transition-all text-sm inline-flex items-center">
                <i class="fas fa-file-image mr-2"></i>Export PNG
            </a>

            <a href="{{ route('super_admin.items.edit', $item) }}" 
               class="bg-yellow-500 hover:bg-yellow-600 hover:shadow-md active:scale-[0.98] text-white px-4 py-2 rounded-lg transition-all text-sm inline-flex items-center">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('super_admin.items.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 hover:shadow-md active:scale-[0.98] text-white px-4 py-2 rounded-lg transition-all text-sm inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        <!-- Left - QR Code & Gambar -->
        <div class="bg-white rounded-xl shadow p-4 sm:p-6 hover:shadow-lg transition-shadow duration-300">
            <!-- QR Code -->
            <div class="text-center mb-4">
                <h3 class="font-semibold text-gray-700 text-sm mb-2">QR Code</h3>
                @if($item->qr_code_url)
                <img src="{{ $item->qr_code_url }}" alt="QR Code" class="mx-auto w-32 sm:w-40">
                <p class="text-xs text-gray-500 mt-2">Scan QR untuk melihat detail</p>
                @else
                <div class="bg-gray-100 p-4 rounded-lg">
                    <i class="fas fa-qrcode text-5xl text-gray-400"></i>
                    <p class="text-gray-500 text-xs mt-1">QR Code belum tersedia</p>
                </div>
                @endif
            </div>
            
            <!-- GAMBAR BARANG -->
            <div class="mt-4">
                <h3 class="font-semibold text-gray-700 text-sm mb-2">Gambar Barang</h3>
                <img src="{{ $item->image_url ?? asset('images/no-image.png') }}" alt="{{ $item->name }}" 
                     class="w-full h-40 sm:h-48 object-cover rounded-lg border border-gray-200 hover:opacity-90 transition-opacity">
            </div>
        </div>

        <!-- Right - Detail -->
        <div class="bg-white rounded-xl shadow p-4 sm:p-6 hover:shadow-lg transition-shadow duration-300">
            <h3 class="font-semibold text-gray-700 text-sm mb-4 border-b pb-2">Informasi Barang</h3>
            
            <div class="space-y-2 text-sm">
                {{-- 🔥 KODE LENGKAP (menggantikan Kode Barang lama) --}}
                <div class="py-2 border-b border-gray-100 rounded px-1 -mx-1" style="background: #0F6B5F08;">
                    <div class="flex items-center gap-1.5 mb-1.5">
                        <i class="fas fa-qrcode text-xs" style="color: #0F6B5F;"></i>
                        <span class="text-xs font-semibold" style="color: #0F6B5F;">Kode Barang</span>
                    </div>
                    <div class="font-mono text-xs font-bold px-2.5 py-1.5 rounded-lg border-2 break-all"
                         style="color: #0F6B5F; background: white; border-color: #0F6B5F40;">
                        {{ $item->full_code ?? $item->code }}
                    </div>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Nama Barang</span>
                    <span class="font-medium">{{ $item->name }}</span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Kategori</span>
                    <span>{{ $item->category->name }}</span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Unit</span>
                    <span>{{ $item->unit->name }}</span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Tanggal Beli</span>
                    <span>{{ $item->purchase_date ? $item->purchase_date->format('d/m/Y') : '-' }}</span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Kondisi</span>
                    <span>
                        <span class="px-2 py-0.5 text-xs rounded-full
                            {{ $item->condition == 'baik' ? 'bg-green-100 text-green-700' : 
                               ($item->condition == 'rusak' ? 'bg-yellow-100 text-yellow-700' : 'bg-orange-100 text-orange-700') }}">
                            {{ ucfirst($item->condition) }}
                        </span>
                    </span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Status</span>
                    <span>
                        <span class="px-2 py-0.5 text-xs rounded-full
                            {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : 
                               ($item->status == 'borrowed' ? 'bg-red-100 text-red-700' : 
                               ($item->status == 'disposed' ? 'bg-gray-200 text-gray-600' : 'bg-yellow-100 text-yellow-700')) }}">
                            {{ $item->status == 'available' ? 'Tersedia' : 
                               ($item->status == 'borrowed' ? 'Dipinjam' : 
                               ($item->status == 'disposed' ? 'Dihapus dari Aset' : 'Perbaikan')) }}
                        </span>
                    </span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Stok</span>
                    <span class="font-semibold {{ $item->stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ $item->stock }} unit
                    </span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Harga</span>
                    <span>{{ $item->price ? 'Rp ' . number_format($item->price, 0, ',', '.') : '-' }}</span>
                </div>

                <!-- SUMBER DANA -->
                <div class="flex justify-between py-1.5 border-b border-gray-100 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Sumber Dana</span>
                    <span>
                        @if($item->fundingSource)
                            <span class="px-2 py-0.5 text-xs rounded-full
                                {{ $item->fundingSource->name == 'BOS' ? 'bg-blue-100 text-blue-700' : 
                                   ($item->fundingSource->name == 'APBY' ? 'bg-green-100 text-green-700' : 
                                   ($item->fundingSource->name == 'WAKAF' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-500')) }}">
                                {{ $item->fundingSource->name }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Lokasi</span>
                    <span>{{ $item->location ?: '-' }}</span>
                </div>

                <div class="flex justify-between py-1.5 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Deskripsi</span>
                    <span class="text-right max-w-[60%]">{{ $item->description ?: '-' }}</span>
                </div>

                <div class="flex justify-between py-1.5 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Dibuat</span>
                    <span>{{ $item->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 🔥 DAFTAR KODE PER STOK                                      --}}
    {{-- ============================================================ --}}
    @if($item->stock > 1 && $item->full_code)
    <div class="mt-4 sm:mt-6 bg-white rounded-xl shadow p-4 sm:p-6 hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center gap-2 mb-3">
            <div class="p-2 rounded-lg shrink-0" style="background: #0F6B5F20; color: #0F6B5F;">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 text-sm sm:text-base">Daftar Kode per Unit Stok</h3>
                <p class="text-xs text-gray-500 mt-0.5">{{ $item->stock }} unit tersedia — masing-masing punya kode unik</p>
            </div>
        </div>

        <div class="rounded-xl border-2 overflow-hidden" style="border-color: #0F6B5F30;">
            <div class="max-h-64 overflow-y-auto">
                <table class="min-w-full text-xs sm:text-sm">
                    <thead class="sticky top-0" style="background: #0F6B5F10;">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold w-16" style="color: #0F6B5F;">Unit</th>
                            <th class="px-3 py-2 text-left font-semibold" style="color: #0F6B5F;">Kode Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($item->getAllStockCodes() as $sc)
                        <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-md font-bold text-xs"
                                      style="background: #0F6B5F20; color: #0F6B5F;">
                                    {{ str_pad($sc['no'], 2, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 font-mono break-all" style="color: #0F6B5F;">
                                {{ $sc['code'] }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Informasi Penyusutan Aset -->
    <div class="mt-4 sm:mt-6 bg-white rounded-xl shadow p-4 sm:p-6 hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center gap-2 mb-5">
            <div class="bg-teal-100 text-teal-600 p-2 rounded-lg">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3 class="font-semibold text-lg">Penyusutan Aset</h3>
        </div>

        @if($item->price && $item->purchase_date)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Nilai Buku -->
                <div class="group relative bg-green-50 border border-green-100 rounded-xl p-4 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-medium text-green-700 uppercase tracking-wide">Nilai Buku Saat Ini</span>
                        <i class="fas fa-wallet text-green-400 group-hover:scale-110 transition-transform"></i>
                    </div>
                    <p class="font-bold text-2xl text-green-600">
                        Rp {{ number_format($item->getBookValue(), 0, ',', '.') }}
                    </p>
                </div>

                <!-- Akumulasi Penyusutan -->
                <div class="group relative bg-red-50 border border-red-100 rounded-xl p-4 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-medium text-red-700 uppercase tracking-wide">Akumulasi Penyusutan</span>
                        <i class="fas fa-arrow-trend-down text-red-400 group-hover:scale-110 transition-transform"></i>
                    </div>
                    <p class="font-bold text-2xl text-red-500">
                        Rp {{ number_format($item->getAccumulatedDepreciation(), 0, ',', '.') }}
                    </p>
                </div>

                <!-- Penyusutan per Tahun -->
                <div class="group relative bg-blue-50 border border-blue-100 rounded-xl p-4 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-medium text-blue-700 uppercase tracking-wide">Penyusutan / Tahun</span>
                        <i class="fas fa-calendar text-blue-400 group-hover:scale-110 transition-transform"></i>
                    </div>
                    <p class="font-bold text-2xl text-blue-600">
                        Rp {{ number_format($item->getAnnualDepreciation(), 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="mb-2 flex justify-between items-center text-sm">
                    <span class="font-medium text-gray-600">Progres Penyusutan</span>
                    <span class="font-bold {{ $item->isFullyDepreciated() ? 'text-red-500' : 'text-teal-600' }}">
                        {{ $item->getDepreciationPercentage() }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div class="h-3 rounded-full transition-all duration-700 ease-out {{ $item->isFullyDepreciated() ? 'bg-gradient-to-r from-red-400 to-red-600' : 'bg-gradient-to-r from-teal-400 to-teal-600' }}"
                         style="width: {{ min($item->getDepreciationPercentage(), 100) }}%"></div>
                </div>

                <p class="text-xs text-gray-400 mt-3 flex items-center gap-1.5">
                    <i class="fas fa-circle-info"></i>
                    Masa manfaat kategori "{{ $item->category->name }}": {{ $item->getUsefulLifeYears() }} tahun.
                    @if($item->isFullyDepreciated())
                        <span class="text-red-500 font-medium ml-1">
                            <i class="fas fa-triangle-exclamation"></i> Barang ini sudah melewati masa manfaatnya.
                        </span>
                    @endif
                </p>
            </div>
        @else
            <div class="text-center py-6">
                <i class="fas fa-circle-info text-gray-300 text-3xl mb-2"></i>
                <p class="text-gray-400 text-sm">
                    Data penyusutan tidak dapat dihitung karena harga atau tanggal pembelian belum diisi.
                </p>
            </div>
        @endif
    </div>

    <!-- Riwayat Peminjaman -->
    <div class="mt-4 sm:mt-6 bg-white rounded-xl shadow p-4 sm:p-6">
        <h3 class="font-semibold text-lg mb-4">Riwayat Peminjaman</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs sm:text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Peminjam</th>
                        <th class="px-4 py-2 text-left">Tanggal Pinjam</th>
                        <th class="px-4 py-2 text-left">Tenggat</th>
                        <th class="px-4 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($item->circulations as $circulation)
                    <tr class="border-t hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-2">{{ $circulation->borrower_name }}</td>
                        <td class="px-4 py-2">{{ $circulation->borrow_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">{{ $circulation->expected_return_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $circulation->status == 'approved' ? 'bg-green-100 text-green-700' : 
                                   ($circulation->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                   ($circulation->status == 'returned' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                                {{ ucfirst($circulation->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                            Belum ada riwayat peminjaman
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection