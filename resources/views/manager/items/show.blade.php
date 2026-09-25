@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-6xl px-3 sm:px-4 lg:px-6 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-box text-brand-600 mr-2"></i>Detail Barang
            </h1>
            <p class="text-sm text-gray-500 mt-1">Informasi lengkap barang & riwayat</p>
        </div>
        <a href="{{ route('manager.items.index') }}" class="text-gray-600 hover:text-gray-800 inline-flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>Kembali
        </a>
    </div>

    @php
        // 🔥 Hitung stok detail
        $totalAktif       = $item->stockCodes()->where('status', '!=', 'disposed')->count();
        $availableStock   = $item->available_stock;
        $borrowedStock    = $item->borrowed_stock;
        $maintenanceStock = $item->maintenance_stock;
        $disposedStock    = $item->disposed_stock_count;

        // 🔥 Ambil riwayat disposals (siapa yang ajukan)
        $disposalHistory = $item->disposals()
            ->with(['user', 'approver'])
            ->latest()
            ->take(10)
            ->get();

        // 🔥 Ambil riwayat circulations
        $circulationHistory = $item->circulations()
            ->with(['user', 'approver'])
            ->latest()
            ->take(10)
            ->get();
    @endphp

    {{-- STOK RINGKASAN --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-xl shadow-sm border-2 border-gray-100 p-4 text-center">
            <div class="w-10 h-10 mx-auto rounded-lg bg-gray-100 flex items-center justify-center mb-2">
                <i class="fas fa-boxes-stacked text-gray-600"></i>
            </div>
            <p class="text-2xl font-bold text-gray-700">{{ $totalAktif }}</p>
            <p class="text-[10px] text-gray-500 uppercase font-semibold">Total Aktif</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border-2 border-emerald-100 p-4 text-center">
            <div class="w-10 h-10 mx-auto rounded-lg bg-emerald-50 flex items-center justify-center mb-2">
                <i class="fas fa-check-circle text-emerald-600"></i>
            </div>
            <p class="text-2xl font-bold text-emerald-600">{{ $availableStock }}</p>
            <p class="text-[10px] text-gray-500 uppercase font-semibold">Tersedia</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border-2 border-amber-100 p-4 text-center">
            <div class="w-10 h-10 mx-auto rounded-lg bg-amber-50 flex items-center justify-center mb-2">
                <i class="fas fa-hand-paper text-amber-600"></i>
            </div>
            <p class="text-2xl font-bold text-amber-600">{{ $borrowedStock }}</p>
            <p class="text-[10px] text-gray-500 uppercase font-semibold">Dipinjam</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border-2 border-red-100 p-4 text-center">
            <div class="w-10 h-10 mx-auto rounded-lg bg-red-50 flex items-center justify-center mb-2">
                <i class="fas fa-trash-can text-red-600"></i>
            </div>
            <p class="text-2xl font-bold text-red-600">{{ $disposedStock }}</p>
            <p class="text-[10px] text-gray-500 uppercase font-semibold">Dihapus</p>
        </div>
    </div>

    {{-- GRID: QR + INFO BARANG --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Left - QR Code -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-lg mb-4 text-center">
                <i class="fas fa-qrcode text-brand-500 mr-2"></i>QR Code
            </h3>
            <div class="text-center">
                @if($item->qr_code_url)
                    <img src="{{ $item->qr_code_url }}" alt="QR Code" class="mx-auto w-64 rounded-xl border border-gray-100">
                    <p class="text-xs text-gray-500 mt-3">Scan QR untuk melihat detail</p>
                @else
                    <div class="bg-gray-50 p-8 rounded-xl border border-dashed border-gray-200">
                        <i class="fas fa-qrcode text-6xl text-gray-300"></i>
                        <p class="text-gray-500 mt-3 text-sm">QR Code belum tersedia</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right - Detail -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-lg mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-brand-500"></i>Informasi Barang
            </h3>

            <div class="space-y-3.5">
                {{-- Kode --}}
                <div>
                    <label class="text-xs text-gray-500 uppercase font-semibold">Kode Barang</label>
                    <div class="inline-flex items-start gap-1 mt-1 px-2 py-1 rounded-md border max-w-full"
                         style="background: #0F6B5F10; border-color: #0F6B5F30;">
                        <i class="fas fa-qrcode text-[9px] mt-0.5 shrink-0" style="color: #0F6B5F;"></i>
                        <span class="font-mono text-[11px] font-semibold leading-tight break-all"
                              style="color: #0F6B5F;">
                            {{ $item->full_code ?? $item->code ?? '-' }}
                        </span>
                    </div>
                </div>

                {{-- Nama --}}
                <div>
                    <label class="text-xs text-gray-500 uppercase font-semibold">Nama Barang</label>
                    <p class="font-medium text-lg text-gray-800 mt-0.5">{{ $item->name }}</p>
                </div>

                {{-- Unit + Kategori --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Unit</label>
                        <p class="mt-0.5">
                            <span class="inline-flex items-center gap-1 text-xs font-medium bg-teal-50 text-teal-700 px-2 py-1 rounded-lg border border-teal-100">
                                <i class="fas fa-building text-[10px]"></i>
                                {{ $item->unit->name ?? '-' }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Kategori</label>
                        <p class="mt-0.5">
                            <span class="inline-flex items-center gap-1 text-xs font-medium bg-blue-50 text-blue-700 px-2 py-1 rounded-lg border border-blue-100">
                                <i class="fas fa-tag text-[10px]"></i>
                                {{ $item->category->name ?? '-' }}
                            </span>
                        </p>
                    </div>
                </div>

                {{-- Kondisi + Status --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Kondisi</label>
                        <p class="mt-0.5">
                            <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded-lg border
                                {{ $item->condition == 'baik' ? 'bg-green-50 text-green-700 border-green-100' :
                                   ($item->condition == 'rusak' ? 'bg-yellow-50 text-yellow-700 border-yellow-100' : 'bg-orange-50 text-orange-700 border-orange-100') }}">
                                <i class="fas fa-circle text-[6px]"></i>
                                {{ ucfirst($item->condition) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Status</label>
                        <p class="mt-0.5">
                            <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded-lg border
                                {{ $item->status == 'available' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' :
                                   ($item->status == 'borrowed' ? 'bg-brand-50 text-brand-700 border-brand-100' :
                                   ($item->status == 'maintenance' ? 'bg-orange-50 text-orange-700 border-orange-100' : 'bg-red-50 text-red-700 border-red-100')) }}">
                                <i class="fas fa-circle text-[6px]"></i>
                                {{ $item->status == 'available' ? 'Tersedia' :
                                   ($item->status == 'borrowed' ? 'Dipinjam' :
                                   ($item->status == 'maintenance' ? 'Perbaikan' : 'Dihapus')) }}
                            </span>
                        </p>
                    </div>
                </div>

                {{-- Lokasi + Harga --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Lokasi</label>
                        <p class="text-sm text-gray-700 mt-0.5">
                            <i class="fas fa-map-marker-alt text-gray-400 text-xs mr-1"></i>
                            {{ $item->location ?: '-' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 uppercase font-semibold">Harga</label>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">
                            Rp {{ number_format((float) ($item->price ?? 0), 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- Sumber Dana --}}
                <div>
                    <label class="text-xs text-gray-500 uppercase font-semibold">Sumber Dana</label>
                    <p class="text-sm text-gray-700 mt-0.5">
                        <i class="fas fa-hand-holding-dollar text-gray-400 text-xs mr-1"></i>
                        {{ $item->fundingSource->name ?? '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 🔥 DAFTAR KODE STOK (detail per unit)                         --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-brand-50 to-transparent flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-100 flex items-center justify-center">
                    <i class="fas fa-barcode text-brand-600 text-xs"></i>
                </span>
                Daftar Kode Stok
                <span class="ml-1 px-2 py-0.5 text-[10px] font-bold rounded-full bg-brand-500 text-white">
                    {{ $totalAktif + $disposedStock }} kode
                </span>
            </h3>
            <span class="text-xs text-gray-400">{{ $availableStock }} tersedia &bull; {{ $borrowedStock }} dipinjam &bull; {{ $disposedStock }} dihapus</span>
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
                {{-- Nomor stok --}}
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-[11px] font-bold shrink-0 bg-white border"
                      style="color: #0F6B5F; border-color: #0F6B5F30;">
                    {{ str_pad($stock->stock_number, 2, '0', STR_PAD_LEFT) }}
                </span>

                {{-- Kode --}}
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

    {{-- ============================================================ --}}
    {{-- 🔥 RIWAYAT PENGAJUAN PENGHAPUSAN (siapa yang pernah ajukan)   --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
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
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase">Diajukan Oleh</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase">Alasan</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase">Approver</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($disposalHistory as $disposal)
                    @php
                        $statusConfig = [
                            'pending'  => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => 'fa-clock', 'label' => 'Menunggu'],
                            'approved' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'fa-check-circle', 'label' => 'Disetujui'],
                            'rejected' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'icon' => 'fa-times-circle', 'label' => 'Ditolak'],
                        ];
                        $sc = $statusConfig[$disposal->status] ?? $statusConfig['pending'];
                    @endphp
                    <tr class="hover:bg-gray-50/70 transition">
                        {{-- Tanggal --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-xs text-gray-700">
                                <i class="fas fa-calendar-alt text-gray-400 text-[10px] mr-1"></i>
                                {{ $disposal->created_at->format('d/m/Y') }}
                            </div>
                            <div class="text-[10px] text-gray-500 mt-0.5">
                                <i class="fas fa-clock text-gray-400 text-[9px] mr-1"></i>
                                {{ $disposal->created_at->format('H:i') }} WIB
                            </div>
                        </td>

                        {{-- Kode stok --}}
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

                        {{-- Pengaju --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-1.5">
                                <div class="w-6 h-6 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white text-[9px] font-bold shrink-0">
                                    {{ strtoupper(substr($disposal->user->name ?? '?', 0, 1)) }}
                                </div>
                                <span class="text-xs text-gray-700">{{ $disposal->user->name ?? '-' }}</span>
                            </div>
                        </td>

                        {{-- Alasan --}}
                        <td class="px-4 py-3 max-w-[250px]">
                            <p class="text-xs text-gray-600 line-clamp-2 leading-snug">{{ $disposal->reason }}</p>
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-semibold rounded-full {{ $sc['bg'] }} {{ $sc['text'] }}">
                                <i class="fas {{ $sc['icon'] }} text-[9px]"></i>
                                {{ $sc['label'] }}
                            </span>
                        </td>

                        {{-- Approver --}}
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">
                            @if($disposal->approver)
                            <div class="flex items-center gap-1.5">
                                <i class="fas fa-user-shield text-emerald-500 text-[10px]"></i>
                                {{ $disposal->approver->name }}
                            </div>
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
            <p class="text-sm">Belum ada pengajuan penghapusan untuk barang ini</p>
        </div>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- 🔥 RIWAYAT PEMINJAMAN                                          --}}
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
                        $statusConfig = [
                            'pending'        => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => 'fa-clock', 'label' => 'Pending'],
                            'approved'       => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => 'fa-check-circle', 'label' => 'Disetujui'],
                            'return_pending' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-rotate-left', 'label' => 'Return Pending'],
                            'returned'       => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'icon' => 'fa-check-double', 'label' => 'Dikembalikan'],
                            'rejected'       => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'fa-times-circle', 'label' => 'Ditolak'],
                        ];
                        $sc = $statusConfig[$circulation->status] ?? $statusConfig['pending'];
                    @endphp
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-xs text-gray-700">
                                <i class="fas fa-calendar-alt text-gray-400 text-[10px] mr-1"></i>
                                {{ $circulation->borrow_date ? $circulation->borrow_date->format('d/m/Y H:i') : '-' }}
                            </div>
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
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-1.5">
                                <div class="w-6 h-6 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white text-[9px] font-bold shrink-0">
                                    {{ strtoupper(substr($circulation->borrower_name ?? '?', 0, 1)) }}
                                </div>
                                <span class="text-xs text-gray-700">{{ $circulation->borrower_name ?? '-' }}</span>
                            </div>
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
            <p class="text-sm">Belum ada riwayat peminjaman untuk barang ini</p>
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