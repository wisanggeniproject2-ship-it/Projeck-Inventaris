@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-3xl px-3 sm:px-4 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                <i class="fas fa-file-lines text-brand-500 mr-2"></i>Detail Peminjaman
            </h1>
            <p class="text-sm text-gray-500 mt-1">Informasi lengkap pengajuan peminjaman</p>
        </div>
        <a href="{{ route('manager.circulations.index') }}"
           class="text-gray-600 hover:text-gray-800 inline-flex items-center gap-2 text-sm">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    {{-- INFO MONITORING --}}
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-3 sm:p-4 mb-5 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-white shadow-sm flex items-center justify-center shrink-0">
            <i class="fas fa-eye text-blue-500"></i>
        </div>
        <div class="text-xs text-blue-700">
            <p class="font-semibold">Mode Monitoring</p>
            <p class="text-[11px] text-blue-600 mt-0.5">Anda hanya bisa melihat, tidak bisa mengubah status</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- HEADER CARD + STATUS --}}
        <div class="border-b border-gray-100 px-5 sm:px-6 py-4">
            <div class="flex flex-wrap justify-between items-center gap-3">
                <h3 class="font-semibold text-base sm:text-lg text-gray-800">
                    <i class="fas fa-circle-info text-gray-400 mr-1.5"></i>
                    Informasi Peminjaman
                </h3>
                @php
                    $statusConfig = [
                        'pending'        => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200', 'icon' => 'fa-clock',        'label' => 'Menunggu'],
                        'approved'       => ['bg' => 'bg-green-100',  'text' => 'text-green-700',  'border' => 'border-green-200',  'icon' => 'fa-check-circle', 'label' => 'Disetujui'],
                        'return_pending' => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'border' => 'border-blue-200',   'icon' => 'fa-rotate',       'label' => 'Proses Kembali'],
                        'returned'       => ['bg' => 'bg-gray-100',   'text' => 'text-gray-700',   'border' => 'border-gray-200',   'icon' => 'fa-box',          'label' => 'Dikembalikan'],
                        'rejected'       => ['bg' => 'bg-red-100',    'text' => 'text-red-700',    'border' => 'border-red-200',    'icon' => 'fa-times-circle', 'label' => 'Ditolak'],
                    ];
                    $sc = $statusConfig[$circulation->status] ?? $statusConfig['pending'];
                @endphp
                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full {{ $sc['bg'] }} {{ $sc['text'] }} border {{ $sc['border'] }}">
                    <i class="fas {{ $sc['icon'] }} text-[10px]"></i>
                    {{ $sc['label'] }}
                </span>
            </div>
        </div>

        <div class="p-5 sm:p-6 space-y-5">

            {{-- 🔥 KODE UNIT YANG DIPINJAM --}}
            <div class="rounded-xl border-2 p-4" style="background: #0F6B5F08; border-color: #0F6B5F30;">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center shrink-0"
                         style="color: #0F6B5F;">
                        <i class="fas fa-barcode"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wide" style="color: #0F6B5F;">
                            Kode Unit yang Dipinjam
                        </p>
                        <p class="text-xs text-gray-500">Kode stok spesifik yang sedang/akan dipinjam user</p>
                    </div>
                </div>

                @if($circulation->stock_code)
                    <div class="font-mono text-xs sm:text-sm font-bold px-3 py-2.5 rounded-lg border-2 break-all bg-white"
                         style="color: #0F6B5F; border-color: #0F6B5F40;">
                        {{ $circulation->stock_code }}
                    </div>
                @else
                    <div class="text-xs text-gray-400 italic">Kode stok tidak tersedia</div>
                @endif
            </div>

            {{-- GRID INFO --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">

                {{-- Barang --}}
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Barang</label>
                    <p class="font-medium text-gray-800 mt-1">{{ $circulation->item->name ?? '-' }}</p>
                    <p class="text-xs text-gray-500 mt-0.5 font-mono break-all">
                        {{ $circulation->item->full_code ?? $circulation->item->code }}
                    </p>
                </div>

                {{-- Unit --}}
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Unit</label>
                    <p class="font-medium text-gray-800 mt-1">
                        {{ $circulation->item->unit->name ?? '-' }}
                    </p>
                </div>

                {{-- Peminjam --}}
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama Peminjam</label>
                    <p class="font-medium text-gray-800 mt-1">{{ $circulation->borrower_name }}</p>
                </div>

                {{-- User Pengaju --}}
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">User Pengaju</label>
                    <p class="font-medium text-gray-800 mt-1">{{ $circulation->user->name ?? '-' }}</p>
                    <p class="text-xs text-gray-500 mt-0.5 break-all">{{ $circulation->user->email ?? '-' }}</p>
                </div>

                {{-- Waktu Pinjam --}}
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Waktu Pinjam</label>
                    <p class="text-sm text-gray-800 mt-1">
                        <i class="fas fa-calendar-alt text-gray-400 text-xs mr-1"></i>
                        {{ $circulation->borrow_date ? $circulation->borrow_date->format('d/m/Y') : '-' }}
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        <i class="fas fa-clock text-gray-400 text-[10px] mr-1"></i>
                        {{ $circulation->borrow_date ? $circulation->borrow_date->format('H:i') : '-' }} WIB
                    </p>
                </div>

                {{-- Tenggat --}}
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tenggat Pengembalian</label>
                    @if($circulation->expected_return_date)
                        @php
                            $isOverdue = method_exists($circulation, 'isOverdue') ? $circulation->isOverdue() : false;
                        @endphp
                        <p class="text-sm mt-1 {{ $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-800' }}">
                            <i class="fas fa-calendar-check text-gray-400 text-xs mr-1"></i>
                            {{ $circulation->expected_return_date->format('d/m/Y') }}
                        </p>
                        <p class="text-xs mt-0.5 {{ $isOverdue ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                            <i class="fas fa-clock text-[10px] mr-1"></i>
                            {{ $circulation->expected_return_date->format('H:i') }} WIB
                        </p>
                        @if($isOverdue)
                            <span class="inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 text-[10px] font-bold rounded-full bg-red-100 text-red-700 border border-red-200">
                                <i class="fas fa-exclamation-triangle text-[9px]"></i>
                                TERLAMBAT
                            </span>
                        @endif
                    @else
                        <p class="text-gray-400 text-sm mt-1">-</p>
                    @endif
                </div>

                {{-- Jam Kembali --}}
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Jam Kembali</label>
                    @if($circulation->return_date)
                        <p class="text-sm text-gray-800 mt-1">
                            <i class="fas fa-calendar-check text-green-500 text-xs mr-1"></i>
                            {{ $circulation->return_date->format('d/m/Y') }}
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            <i class="fas fa-clock text-gray-400 text-[10px] mr-1"></i>
                            {{ $circulation->return_date->format('H:i') }} WIB
                        </p>
                    @else
                        <p class="text-xs text-gray-400 italic mt-1">Belum dikembalikan</p>
                    @endif
                </div>

                {{-- Disetujui --}}
                @if($circulation->approved_by)
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Disetujui Oleh</label>
                        <p class="font-medium text-gray-800 mt-1">{{ $circulation->approver->name ?? '-' }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $circulation->approved_at ? $circulation->approved_at->format('d/m/Y H:i') : '-' }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- TUJUAN --}}
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tujuan Peminjaman</label>
                <div class="mt-1.5 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                    <p class="text-sm text-gray-700 whitespace-pre-line break-words">
                        {{ $circulation->purpose ?: '-' }}
                    </p>
                </div>
            </div>

            {{-- ALASAN DITOLAK --}}
            @if($circulation->status == 'rejected' && $circulation->rejection_reason)
                <div class="p-3 bg-red-50 border-l-4 border-red-400 rounded-r-lg">
                    <p class="text-xs font-semibold text-red-700 uppercase mb-1">
                        <i class="fas fa-info-circle mr-1"></i>Alasan Ditolak
                    </p>
                    <p class="text-sm text-red-700 leading-snug break-words">{{ $circulation->rejection_reason }}</p>
                    @if($circulation->rejected_at)
                        <p class="mt-1.5 text-[11px] text-red-500/80">
                            <i class="fas fa-clock mr-1"></i>
                            {{ $circulation->rejected_at->format('d/m/Y H:i') }}
                        </p>
                    @endif
                </div>
            @endif

        </div>
    </div>
</div>
@endsection