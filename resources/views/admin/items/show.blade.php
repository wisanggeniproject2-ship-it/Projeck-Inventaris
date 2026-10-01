@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-5xl px-3 sm:px-4 lg:px-6 py-4 sm:py-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-5">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                <i class="fas fa-box text-brand-500 mr-2"></i>Detail Barang
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Informasi lengkap barang, stok, dan riwayat</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('super_admin.items.pdf', $item) }}"
               target="_blank"
               class="bg-red-500 hover:bg-red-600 text-white px-3 sm:px-4 py-2 rounded-lg transition text-xs sm:text-sm inline-flex items-center gap-1.5">
                <i class="fas fa-file-pdf"></i>
                <span class="hidden sm:inline">Cetak PDF</span>
            </a>
            <a href="{{ route('super_admin.items.png', $item) }}"
               download
               class="bg-teal-600 hover:bg-teal-700 text-white px-3 sm:px-4 py-2 rounded-lg transition text-xs sm:text-sm inline-flex items-center gap-1.5">
                <i class="fas fa-file-image"></i>
                <span class="hidden sm:inline">Export PNG</span>
            </a>
            <a href="{{ route('super_admin.items.edit', $item) }}"
               class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 sm:px-4 py-2 rounded-lg transition text-xs sm:text-sm inline-flex items-center gap-1.5">
                <i class="fas fa-edit"></i>
                <span class="hidden sm:inline">Edit</span>
            </a>
            <a href="{{ route('super_admin.items.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-3 sm:px-4 py-2 rounded-lg transition text-xs sm:text-sm inline-flex items-center gap-1.5">
                <i class="fas fa-arrow-left"></i>
                <span class="hidden sm:inline">Kembali</span>
            </a>
        </div>
    </div>

    @php
        $totalAktif       = $item->stockCodes()->where('status', '!=', 'disposed')->count();
        $availableStock   = $item->available_stock ?? 0;
        $borrowedStock    = $item->borrowed_stock ?? 0;
        $maintenanceStock = $item->maintenance_stock ?? 0;
        $disposedStock    = $item->disposed_stock_count ?? 0;

        $disposalHistory = $item->disposals()
            ->with(['user', 'approver'])
            ->latest()
            ->take(10)
            ->get();

        $circulationHistory = $item->circulations()
            ->with(['user', 'approver'])
            ->latest()
            ->take(10)
            ->get();
    @endphp

    {{-- STOK RINGKASAN --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
        <div class="bg-white rounded-xl shadow-sm border-2 border-gray-100 p-3 sm:p-4 text-center">
            <div class="w-9 h-9 mx-auto rounded-lg bg-gray-100 flex items-center justify-center mb-1.5">
                <i class="fas fa-boxes-stacked text-gray-600 text-sm"></i>
            </div>
            <p class="text-xl sm:text-2xl font-bold text-gray-700">{{ $totalAktif }}</p>
            <p class="text-[10px] text-gray-500 uppercase font-semibold">Total Aktif</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border-2 border-emerald-100 p-3 sm:p-4 text-center">
            <div class="w-9 h-9 mx-auto rounded-lg bg-emerald-50 flex items-center justify-center mb-1.5">
                <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
            </div>
            <p class="text-xl sm:text-2xl font-bold text-emerald-600">{{ $availableStock }}</p>
            <p class="text-[10px] text-gray-500 uppercase font-semibold">Tersedia</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border-2 border-amber-100 p-3 sm:p-4 text-center">
            <div class="w-9 h-9 mx-auto rounded-lg bg-amber-50 flex items-center justify-center mb-1.5">
                <i class="fas fa-hand-paper text-amber-600 text-sm"></i>
            </div>
            <p class="text-xl sm:text-2xl font-bold text-amber-600">{{ $borrowedStock }}</p>
            <p class="text-[10px] text-gray-500 uppercase font-semibold">Dipinjam</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border-2 border-red-100 p-3 sm:p-4 text-center">
            <div class="w-9 h-9 mx-auto rounded-lg bg-red-50 flex items-center justify-center mb-1.5">
                <i class="fas fa-trash-can text-red-600 text-sm"></i>
            </div>
            <p class="text-xl sm:text-2xl font-bold text-red-600">{{ $disposedStock }}</p>
            <p class="text-[10px] text-gray-500 uppercase font-semibold">Dihapus</p>
        </div>
    </div>

    {{-- GRID: QR + INFO BARANG --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-5">
        {{-- LEFT: QR + Gambar --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
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

            <div class="mt-4">
                <h3 class="font-semibold text-gray-700 text-sm mb-2">Gambar Barang</h3>
                <img src="{{ $item->image_url ?? asset('images/no-image.png') }}" alt="{{ $item->name }}"
                     class="w-full h-40 sm:h-48 object-cover rounded-lg border border-gray-200">
            </div>
        </div>

        {{-- RIGHT: Info Barang --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <h3 class="font-semibold text-gray-700 text-sm mb-4 border-b pb-2">Informasi Barang</h3>

            <div class="space-y-2 text-sm">
                {{-- Kode Barang --}}
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

                <div class="flex justify-between py-1.5 border-b border-gray-100">
                    <span class="text-gray-500">Nama Barang</span>
                    <span class="font-medium text-right">{{ $item->name }}</span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100">
                    <span class="text-gray-500">Kategori</span>
                    <span>{{ $item->category->name ?? '-' }}</span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100">
                    <span class="text-gray-500">Unit</span>
                    <span>{{ $item->unit->name ?? '-' }}</span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100">
                    <span class="text-gray-500">Tanggal Beli</span>
                    <span>{{ $item->purchase_date ? $item->purchase_date->format('d/m/Y') : '-' }}</span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100">
                    <span class="text-gray-500">Kondisi</span>
                    <span>
                        <span class="px-2 py-0.5 text-xs rounded-full
                            {{ $item->condition == 'baik' ? 'bg-green-100 text-green-700' :
                               ($item->condition == 'rusak' ? 'bg-yellow-100 text-yellow-700' : 'bg-orange-100 text-orange-700') }}">
                            {{ ucfirst($item->condition) }}
                        </span>
                    </span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100">
                    <span class="text-gray-500">Status</span>
                    <span>
                        <span class="px-2 py-0.5 text-xs rounded-full
                            {{ $item->status == 'available' ? 'bg-green-100 text-green-700' :
                               ($item->status == 'borrowed' ? 'bg-red-100 text-red-700' :
                               ($item->status == 'disposed' ? 'bg-gray-200 text-gray-600' : 'bg-yellow-100 text-yellow-700')) }}">
                            {{ $item->status == 'available' ? 'Tersedia' :
                               ($item->status == 'borrowed' ? 'Dipinjam' :
                               ($item->status == 'disposed' ? 'Dihapus' : 'Perbaikan')) }}
                        </span>
                    </span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100">
                    <span class="text-gray-500">Stok</span>
                    <span class="font-semibold {{ $item->stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ $item->stock }} unit
                    </span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100">
                    <span class="text-gray-500">Harga</span>
                    <span>{{ $item->price ? 'Rp ' . number_format($item->price, 0, ',', '.') : '-' }}</span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100">
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

                <div class="flex justify-between py-1.5 border-b border-gray-100">
                    <span class="text-gray-500">Lokasi</span>
                    <span>{{ $item->location ?: '-' }}</span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100">
                    <span class="text-gray-500">Deskripsi</span>
                    <span class="text-right max-w-[60%]">{{ $item->description ?: '-' }}</span>
                </div>

                <div class="flex justify-between py-1.5">
                    <span class="text-gray-500">Dibuat</span>
                    <span>{{ $item->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 🔥 DAFTAR KODE STOK (pakai relasi stockCodes dengan status)    --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-5 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-brand-50 to-transparent flex flex-wrap items-center justify-between gap-2">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-100 flex items-center justify-center">
                    <i class="fas fa-barcode text-brand-600 text-xs"></i>
                </span>
                Daftar Kode Stok
                <span class="ml-1 px-2 py-0.5 text-[10px] font-bold rounded-full bg-brand-500 text-white">
                    {{ $totalAktif + $disposedStock }} kode
                </span>
            </h3>
            <span class="text-xs text-gray-400">
                {{ $availableStock }} tersedia &bull; {{ $borrowedStock }} dipinjam &bull; {{ $disposedStock }} dihapus
            </span>
        </div>

        @if($item->stockCodes->count() > 0)
        <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 max-h-96 overflow-y-auto thin-scroll">
            @foreach($item->stockCodes as $stock)
            @php
                $statusConfig = [
                    'available'   => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-700', 'icon' => 'fa-check-circle', 'label' => 'Tersedia'],
                    'borrowed'    => ['bg' => 'bg-amber-50',   'border' => 'border-amber-200',   'text' => 'text-amber-700',   'icon' => 'fa-hand-paper',    'label' => 'Dipinjam'],
                    'maintenance' => ['bg' => 'bg-orange-50',  'border' => 'border-orange-200',  'text' => 'text-orange-700',  'icon' => 'fa-tools',         'label' => 'Perbaikan'],
                    'disposed'    => ['bg' => 'bg-red-50',     'border' => 'border-red-200',     'text' => 'text-red-700',     'icon' => 'fa-trash-can',     'label' => 'Dihapus'],
                ];
                $sc = $statusConfig[$stock->status] ?? $statusConfig['available'];
            @endphp
            <div class="flex items-center gap-2 p-2.5 rounded-xl border-2 {{ $sc['bg'] }} {{ $sc['border'] }}">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-[11px] font-bold shrink-0 bg-white border"
                      style="color: #0F6B5F; border-color: #0F6B5F30;">
                    {{ str_pad($stock->stock_number, 2, '0', STR_PAD_LEFT) }}
                </span>
                <div class="flex-1 min-w-0">
                    <p class="font-mono text-[9px] font-semibold break-all leading-tight" style="color: #0F6B5F;">
                        {{ $stock->stock_code }}
                    </p>
                    <p class="text-[10px] font-medium mt-0.5 {{ $sc['text'] }} inline-flex items-center gap-1">
                        <i class="fas {{ $sc['icon'] }} text-[8px]"></i>
                        {{ $sc['label'] }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-8 text-center text-gray-400">
            <i class="fas fa-barcode text-3xl mb-2 block"></i>
            <p class="text-sm">Belum ada kode stok</p>
        </div>
        @endif
    </div>

    {{-- PENYUSUTAN --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-5">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-9 h-9 rounded-lg bg-teal-100 flex items-center justify-center">
                <i class="fas fa-chart-line text-teal-600 text-sm"></i>
            </div>
            <h3 class="font-semibold text-base sm:text-lg">Penyusutan Aset</h3>
        </div>

        @if($item->price && $item->purchase_date)
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
                <div class="bg-green-50 border border-green-100 rounded-xl p-4">
                    <p class="text-xs font-medium text-green-700 uppercase mb-1">Nilai Buku Saat Ini</p>
                    <p class="font-bold text-lg sm:text-xl text-green-600">
                        Rp {{ number_format($item->getBookValue(), 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-red-50 border border-red-100 rounded-xl p-4">
                    <p class="text-xs font-medium text-red-700 uppercase mb-1">Akumulasi Penyusutan</p>
                    <p class="font-bold text-lg sm:text-xl text-red-500">
                        Rp {{ number_format($item->getAccumulatedDepreciation(), 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                    <p class="text-xs font-medium text-blue-700 uppercase mb-1">Penyusutan / Tahun</p>
                    <p class="font-bold text-lg sm:text-xl text-blue-600">
                        Rp {{ number_format($item->getAnnualDepreciation(), 0, ',', '.') }}
                    </p>
                </div>
            </div>

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
                <p class="text-xs text-gray-400 mt-3">
                    <i class="fas fa-circle-info mr-1"></i>
                    Masa manfaat kategori "{{ $item->category->name }}": {{ $item->getUsefulLifeYears() }} tahun.
                    @if($item->isFullyDepreciated())
                        <span class="text-red-500 font-medium ml-1">
                            <i class="fas fa-triangle-exclamation"></i> Barang sudah melewati masa manfaat.
                        </span>
                    @endif
                </p>
            </div>
        @else
            <div class="text-center py-6">
                <i class="fas fa-circle-info text-gray-300 text-3xl mb-2"></i>
                <p class="text-gray-400 text-sm">Data penyusutan belum bisa dihitung.</p>
            </div>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- 🔥 RIWAYAT PENGAJUAN PENGHAPUSAN                              --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-5 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-red-50 to-transparent flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center">
                    <i class="fas fa-trash-can text-red-600 text-xs"></i>
                </span>
                Riwayat Pengajuan Penghapusan
                @if($disposalHistory->count() > 0)
                    <span class="ml-1 px-2 py-0.5 text-[10px] font-bold rounded-full bg-red-500 text-white">
                        {{ $disposalHistory->count() }}
                    </span>
                @endif
            </h3>
        </div>

        @if($disposalHistory->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase">Kode Stok</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase">Pengaju</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase">Alasan</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase">Approver</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($disposalHistory as $disposal)
                    @php
                        $sc = [
                            'pending'  => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => 'fa-clock', 'label' => 'Menunggu'],
                            'approved' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'fa-check-circle', 'label' => 'Disetujui'],
                            'rejected' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'icon' => 'fa-times-circle', 'label' => 'Ditolak'],
                        ][$disposal->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'icon' => 'fa-clock', 'label' => 'Menunggu'];
                    @endphp
                    <tr class="hover:bg-gray-50/70">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-xs text-gray-700">
                                {{ $disposal->created_at->format('d/m/Y') }}
                            </div>
                            <div class="text-[10px] text-gray-500">
                                {{ $disposal->created_at->format('H:i') }} WIB
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($disposal->stock_code)
                                <div class="inline-flex items-start gap-1 px-1.5 py-0.5 rounded-md text-[9px] font-mono font-bold break-all"
                                     style="background: #0F6B5F15; color: #0F6B5F;">
                                    <i class="fas fa-barcode text-[8px] mt-0.5 shrink-0"></i>
                                    {{ $disposal->stock_code }}
                                </div>
                            @else
                                <span class="text-[10px] text-gray-400 italic">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-700">
                            {{ $disposal->user->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 max-w-[250px]">
                            <p class="text-xs text-gray-600 line-clamp-2 leading-snug">{{ $disposal->reason }}</p>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-semibold rounded-full {{ $sc['bg'] }} {{ $sc['text'] }}">
                                <i class="fas {{ $sc['icon'] }} text-[9px]"></i>
                                {{ $sc['label'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">
                            @if($disposal->approver)
                                <i class="fas fa-user-shield text-emerald-500 text-[10px] mr-1"></i>
                                {{ $disposal->approver->name }}
                            @else
                                <span class="italic text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-8 text-center text-gray-400">
            <i class="fas fa-inbox text-3xl mb-2 block"></i>
            <p class="text-sm">Belum ada pengajuan penghapusan</p>
        </div>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- 🔥 RIWAYAT PEMINJAMAN (dengan kode stok)                     --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-brand-50 to-transparent flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-100 flex items-center justify-center">
                    <i class="fas fa-right-left text-brand-600 text-xs"></i>
                </span>
                Riwayat Peminjaman
                @if($circulationHistory->count() > 0)
                    <span class="ml-1 px-2 py-0.5 text-[10px] font-bold rounded-full bg-brand-500 text-white">
                        {{ $circulationHistory->count() }}
                    </span>
                @endif
            </h3>
        </div>

        @if($circulationHistory->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase">Tanggal Pinjam</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase">Kode Stok</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase">Peminjam</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase">Tenggat</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($circulationHistory as $circulation)
                    @php
                        $sc = [
                            'pending'        => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => 'fa-clock', 'label' => 'Pending'],
                            'approved'       => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => 'fa-check-circle', 'label' => 'Disetujui'],
                            'return_pending' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-rotate-left', 'label' => 'Return Pending'],
                            'returned'       => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'icon' => 'fa-check-double', 'label' => 'Dikembalikan'],
                            'rejected'       => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'fa-times-circle', 'label' => 'Ditolak'],
                        ][$circulation->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'icon' => 'fa-clock', 'label' => 'Pending'];
                    @endphp
                    <tr class="hover:bg-gray-50/70">
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-700">
                            {{ $circulation->borrow_date ? $circulation->borrow_date->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($circulation->stock_code)
                                <div class="inline-flex items-start gap-1 px-1.5 py-0.5 rounded-md text-[9px] font-mono font-bold break-all"
                                     style="background: #0F6B5F15; color: #0F6B5F;">
                                    <i class="fas fa-barcode text-[8px] mt-0.5 shrink-0"></i>
                                    {{ $circulation->stock_code }}
                                </div>
                            @else
                                <span class="text-[10px] text-gray-400 italic">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-700">
                            {{ $circulation->borrower_name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">
                            {{ $circulation->expected_return_date ? $circulation->expected_return_date->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-semibold rounded-full {{ $sc['bg'] }} {{ $sc['text'] }}">
                                <i class="fas {{ $sc['icon'] }} text-[9px]"></i>
                                {{ $sc['label'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-8 text-center text-gray-400">
            <i class="fas fa-inbox text-3xl mb-2 block"></i>
            <p class="text-sm">Belum ada riwayat peminjaman</p>
        </div>
        @endif
    </div>

</div>

@push('styles')
<style>
    .thin-scroll::-webkit-scrollbar { width: 6px; }
    .thin-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    .thin-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .thin-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
@endsection