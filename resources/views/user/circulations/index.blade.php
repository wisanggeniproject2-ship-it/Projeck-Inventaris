@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-7xl px-3 sm:px-4 lg:px-6 py-4 sm:py-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                <i class="fas fa-clock-rotate-left text-brand-500 mr-2"></i>Riwayat Peminjaman Saya
            </h1>
            <p class="text-sm text-gray-500 mt-1">Daftar semua peminjaman yang pernah Anda ajukan</p>
        </div>
        <a href="{{ route('user.items.index') }}" 
           class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-4 py-2.5 rounded-xl whitespace-nowrap shadow-sm hover:shadow-md transition-all text-sm font-medium inline-flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Ajukan Peminjaman
        </a>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2 mb-6">
        <a href="{{ route('user.circulations.index') }}" 
           class="bg-white rounded-xl shadow-sm p-3 text-center hover:shadow-md transition border-2 {{ !request('status') ? 'border-brand-500' : 'border-transparent' }}">
            <p class="text-2xl font-bold text-gray-800">{{ $stats['all'] }}</p>
            <p class="text-xs text-gray-500">Semua</p>
        </a>
        <a href="{{ route('user.circulations.index', ['status' => 'pending']) }}" 
           class="bg-white rounded-xl shadow-sm p-3 text-center hover:shadow-md transition border-2 {{ request('status') == 'pending' ? 'border-yellow-500' : 'border-transparent' }}">
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            <p class="text-xs text-gray-500">Menunggu</p>
        </a>
        <a href="{{ route('user.circulations.index', ['status' => 'approved']) }}" 
           class="bg-white rounded-xl shadow-sm p-3 text-center hover:shadow-md transition border-2 {{ request('status') == 'approved' ? 'border-green-500' : 'border-transparent' }}">
            <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</p>
            <p class="text-xs text-gray-500">Disetujui</p>
        </a>
        <a href="{{ route('user.circulations.index', ['status' => 'returned']) }}" 
           class="bg-white rounded-xl shadow-sm p-3 text-center hover:shadow-md transition border-2 {{ request('status') == 'returned' ? 'border-blue-500' : 'border-transparent' }}">
            <p class="text-2xl font-bold text-blue-600">{{ $stats['returned'] }}</p>
            <p class="text-xs text-gray-500">Dikembalikan</p>
        </a>
        <a href="{{ route('user.circulations.index', ['status' => 'rejected']) }}" 
           class="bg-white rounded-xl shadow-sm p-3 text-center hover:shadow-md transition border-2 {{ request('status') == 'rejected' ? 'border-red-500' : 'border-transparent' }}">
            <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
            <p class="text-xs text-gray-500">Ditolak</p>
        </a>
    </div>

    {{-- SEARCH --}}
    <div class="mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-2">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nama barang, kode, atau kode stok..." 
                       class="w-full pl-10 pr-4 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition">
            </div>
            <div class="flex gap-2">
                <button type="submit" 
                        class="bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-white px-6 py-2.5 rounded-xl whitespace-nowrap shadow-sm hover:shadow-md transition-all text-sm font-medium inline-flex items-center gap-2">
                    <i class="fas fa-search"></i>Cari
                </button>
                @if(request('search'))
                <a href="{{ route('user.circulations.index', ['status' => request('status')]) }}" 
                   class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2.5 rounded-xl whitespace-nowrap transition inline-flex items-center justify-center"
                   title="Reset">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ============================================================ --}}
    {{-- 📱 MOBILE VIEW — Card Layout                                --}}
    {{-- ============================================================ --}}
    <div class="block md:hidden space-y-3 mb-5">
        @forelse($circulations as $circulation)
        @php
            $isOverdue = method_exists($circulation, 'isOverdue')
                ? ($circulation->isOverdue() ?? false)
                : false;
            $statusConfig = [
                'pending'        => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200', 'icon' => 'fa-clock',           'label' => 'Menunggu'],
                'approved'       => ['bg' => 'bg-green-100',  'text' => 'text-green-700',  'border' => 'border-green-200',  'icon' => 'fa-check-circle',    'label' => 'Disetujui'],
                'return_pending' => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'border' => 'border-blue-200',   'icon' => 'fa-rotate',          'label' => 'Proses Kembali'],
                'returned'       => ['bg' => 'bg-gray-100',   'text' => 'text-gray-700',   'border' => 'border-gray-200',   'icon' => 'fa-box',             'label' => 'Dikembalikan'],
                'rejected'       => ['bg' => 'bg-red-100',    'text' => 'text-red-700',    'border' => 'border-red-200',    'icon' => 'fa-times-circle',    'label' => 'Ditolak'],
            ];
            $sc = $statusConfig[$circulation->status] ?? $statusConfig['pending'];
        @endphp

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
            {{-- Header Card --}}
            <div class="p-4 border-b border-gray-100">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <h3 class="font-bold text-sm text-gray-800 flex-1 min-w-0 line-clamp-2">
                        {{ $circulation->item->name ?? '-' }}
                    </h3>
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-semibold rounded-full {{ $sc['bg'] }} {{ $sc['text'] }} border {{ $sc['border'] }} shrink-0">
                        <i class="fas {{ $sc['icon'] }} text-[9px]"></i>
                        {{ $sc['label'] }}
                    </span>
                </div>

                {{-- 🔥 KODE STOK SAJA (tanpa full_code) --}}
                @if($circulation->stock_code)
                    <div class="inline-flex items-start gap-1 px-2 py-1 rounded-md border max-w-full"
                         style="background: #0F6B5F10; border-color: #0F6B5F30;">
                        <i class="fas fa-barcode text-[9px] mt-0.5 shrink-0" style="color: #0F6B5F;"></i>
                        <span class="font-mono text-[10px] font-semibold leading-tight"
                              style="color: #0F6B5F; word-break: break-word; overflow-wrap: anywhere;">
                            {{ $circulation->stock_code }}
                        </span>
                    </div>
                @endif

                {{-- Unit --}}
                <div class="mt-2">
                    <span class="inline-flex items-center gap-1 text-[10px] font-medium bg-teal-50 text-teal-700 px-2 py-0.5 rounded border border-teal-100">
                        <i class="fas fa-building text-[9px]"></i>
                        {{ $circulation->item->unit->name ?? '-' }}
                    </span>
                </div>
            </div>

            {{-- Body --}}
            <div class="p-4 space-y-3">
                {{-- Jam Pinjam & Tenggat --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-[10px] font-semibold text-gray-500 uppercase mb-1">
                            <i class="fas fa-sign-out-alt mr-1"></i>Jam Pinjam
                        </p>
                        <p class="text-xs text-gray-700 font-medium">
                            {{ $circulation->borrow_date ? $circulation->borrow_date->format('d/m/Y') : '-' }}
                        </p>
                        <p class="text-[10px] text-gray-500">
                            {{ $circulation->borrow_date ? $circulation->borrow_date->format('H:i') : '-' }} WIB
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold text-gray-500 uppercase mb-1">
                            <i class="fas fa-hourglass-half mr-1"></i>Tenggat
                        </p>
                        @if($circulation->expected_return_date)
                            <p class="text-xs font-medium {{ $isOverdue ? 'text-red-600' : 'text-gray-700' }}">
                                {{ $circulation->expected_return_date->format('d/m/Y') }}
                            </p>
                            <p class="text-[10px] {{ $isOverdue ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                {{ $circulation->expected_return_date->format('H:i') }} WIB
                            </p>
                            @if($isOverdue)
                                <span class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 text-[9px] font-bold rounded-full bg-red-100 text-red-700 border border-red-200">
                                    <i class="fas fa-exclamation-triangle text-[8px]"></i>
                                    TERLAMBAT
                                </span>
                            @endif
                        @else
                            <p class="text-xs text-gray-400">-</p>
                        @endif
                    </div>
                </div>

                {{-- Jam Kembali --}}
                <div>
                    <p class="text-[10px] font-semibold text-gray-500 uppercase mb-1">
                        <i class="fas fa-sign-in-alt mr-1"></i>Jam Kembali
                    </p>
                    @if($circulation->return_date)
                        <p class="text-xs text-gray-700 font-medium">
                            {{ $circulation->return_date->format('d/m/Y') }}
                            <span class="text-gray-500">— {{ $circulation->return_date->format('H:i') }} WIB</span>
                        </p>
                    @else
                        <p class="text-xs text-gray-400 italic">Belum dikembalikan</p>
                    @endif
                </div>

                {{-- Alasan reject --}}
                @if($circulation->status == 'rejected' && $circulation->rejection_reason)
                    <div class="p-3 bg-red-50 border-l-4 border-red-400 rounded-r-lg">
                        <p class="text-[10px] font-semibold text-red-700 uppercase mb-1">
                            <i class="fas fa-info-circle mr-1"></i>Alasan Ditolak
                        </p>
                        <p class="text-xs text-red-700 leading-snug break-words">{{ $circulation->rejection_reason }}</p>
                        @if($circulation->rejected_at)
                            <p class="mt-1.5 text-[10px] text-red-500/80">
                                <i class="fas fa-clock mr-1"></i>
                                {{ $circulation->rejected_at->format('d/m/Y H:i') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Footer: Aksi --}}
            <div class="p-3 border-t border-gray-100 bg-gray-50 flex flex-wrap gap-2">
                {{-- Detail Barang --}}
                <a href="{{ route('user.items.show', $circulation->item_id) }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white border border-blue-200 text-blue-600 hover:bg-blue-50 text-xs font-medium transition"
                   title="Detail Barang">
                    <i class="fas fa-eye text-[10px]"></i>
                    <span>Detail</span>
                </a>

                {{-- Ajukan Pengembalian --}}
                @if($circulation->status == 'approved')
                    <form action="{{ route('user.circulations.requestReturn', $circulation) }}" method="POST" class="inline">
                        @csrf
                        @method('PUT')
                        <button type="submit" 
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white text-xs font-medium transition"
                                onclick="return confirm('Yakin ingin mengajukan pengembalian barang ini?')">
                            <i class="fas fa-undo-alt text-[10px]"></i>
                            <span>Ajukan Kembali</span>
                        </button>
                    </form>
                @endif

                {{-- Menunggu Konfirmasi --}}
                @if($circulation->status == 'return_pending')
                    <span class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-blue-100 text-blue-700 border border-blue-200 text-xs font-medium">
                        <i class="fas fa-clock text-[10px]"></i>
                        Menunggu Konfirmasi
                    </span>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl shadow-sm p-8 text-center border border-gray-100">
            <div class="w-20 h-20 mx-auto mb-3 rounded-full bg-gray-100 flex items-center justify-center">
                <i class="fas fa-inbox text-3xl text-gray-300"></i>
            </div>
            <p class="text-gray-600 font-medium mb-1">Belum ada riwayat peminjaman</p>
            <p class="text-gray-400 text-sm mb-4">Silakan ajukan peminjaman barang terlebih dahulu</p>
            <a href="{{ route('user.items.index') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-xl text-sm font-medium transition shadow-sm hover:shadow-md">
                <i class="fas fa-plus"></i>
                Ajukan Peminjaman
            </a>
        </div>
        @endforelse
    </div>

    {{-- ============================================================ --}}
    {{-- 💻 DESKTOP VIEW — Tabel (6 KOLOM, LEBIH LEGA)               --}}
    {{-- ============================================================ --}}
    <div class="hidden md:block bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" style="table-layout: fixed;">
                <colgroup>
                    <col style="width: 22%;">   {{-- Barang + Kode Stok --}}
                    <col style="width: 10%;">   {{-- Unit --}}
                    <col style="width: 14%;">   {{-- Jam Pinjam --}}
                    <col style="width: 16%;">   {{-- Tenggat --}}
                    <col style="width: 14%;">   {{-- Jam Kembali --}}
                    <col style="width: 14%;">   {{-- Status --}}
                    <col style="width: 10%;">   {{-- Aksi --}}
                </colgroup>
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-box text-gray-400 mr-1"></i>Barang & Kode
                        </th>
                        <th class="px-3 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-building text-gray-400 mr-1"></i>Unit
                        </th>
                        <th class="px-3 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-sign-out-alt text-gray-400 mr-1"></i>Jam Pinjam
                        </th>
                        <th class="px-3 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-hourglass-half text-gray-400 mr-1"></i>Tenggat
                        </th>
                        <th class="px-3 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-sign-in-alt text-gray-400 mr-1"></i>Jam Kembali
                        </th>
                        <th class="px-3 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-info-circle text-gray-400 mr-1"></i>Status
                        </th>
                        <th class="px-3 py-3 text-center text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-cog text-gray-400 mr-1"></i>Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($circulations as $circulation)
                    @php
                        $isOverdue = method_exists($circulation, 'isOverdue')
                            ? ($circulation->isOverdue() ?? false)
                            : false;
                        $statusConfig = [
                            'pending'        => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200', 'icon' => 'fa-clock',        'label' => 'Menunggu'],
                            'approved'       => ['bg' => 'bg-green-100',  'text' => 'text-green-700',  'border' => 'border-green-200',  'icon' => 'fa-check-circle', 'label' => 'Disetujui'],
                            'return_pending' => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'border' => 'border-blue-200',   'icon' => 'fa-rotate',       'label' => 'Proses Kembali'],
                            'returned'       => ['bg' => 'bg-gray-100',   'text' => 'text-gray-700',   'border' => 'border-gray-200',   'icon' => 'fa-box',          'label' => 'Dikembalikan'],
                            'rejected'       => ['bg' => 'bg-red-100',    'text' => 'text-red-700',    'border' => 'border-red-200',    'icon' => 'fa-times-circle', 'label' => 'Ditolak'],
                        ];
                        $sc = $statusConfig[$circulation->status] ?? $statusConfig['pending'];
                    @endphp
                    <tr class="hover:bg-gray-50/70 transition
                        {{ $circulation->status == 'pending' ? 'bg-yellow-50/50' : '' }}
                        {{ $circulation->status == 'rejected' ? 'bg-red-50/40' : '' }}
                        {{ $isOverdue ? 'bg-red-50/60' : '' }}">

                        {{-- BARANG + KODE STOK --}}
                        <td class="px-3 py-3">
                            <div class="text-sm font-medium text-gray-800 leading-tight line-clamp-2">
                                {{ $circulation->item->name ?? '-' }}
                            </div>

                            {{-- 🔥 KODE STOK SAJA (kode lengkap dihapus) --}}
                            @if($circulation->stock_code)
                                <div class="inline-flex items-start gap-1 mt-1.5 px-1.5 py-0.5 rounded-md border max-w-full"
                                     style="background: #0F6B5F10; border-color: #0F6B5F30;">
                                    <i class="fas fa-barcode text-[9px] mt-0.5 shrink-0" style="color: #0F6B5F;"></i>
                                    <span class="font-mono text-[10px] font-semibold leading-tight"
                                          style="color: #0F6B5F; word-break: break-word; overflow-wrap: anywhere;">
                                        {{ $circulation->stock_code }}
                                    </span>
                                </div>
                            @else
                                <div class="text-[10px] text-gray-400 italic mt-1">-</div>
                            @endif
                        </td>

                        {{-- UNIT --}}
                        <td class="px-3 py-3">
                            <span class="inline-flex items-center gap-1 text-[10px] font-medium bg-teal-50 text-teal-700 px-1.5 py-0.5 rounded border border-teal-100 whitespace-nowrap">
                                <i class="fas fa-building text-[9px]"></i>
                                {{ $circulation->item->unit->name ?? '-' }}
                            </span>
                        </td>

                        {{-- JAM PINJAM --}}
                        <td class="px-3 py-3">
                            <div class="text-xs font-medium text-gray-800 leading-tight">
                                <i class="fas fa-calendar-alt text-gray-400 text-[10px] mr-1"></i>
                                {{ $circulation->borrow_date ? $circulation->borrow_date->format('d/m/Y') : '-' }}
                            </div>
                            <div class="text-[10px] text-gray-500 mt-0.5">
                                <i class="fas fa-clock text-gray-400 text-[9px] mr-1"></i>
                                {{ $circulation->borrow_date ? $circulation->borrow_date->format('H:i') : '-' }} WIB
                            </div>
                        </td>

                        {{-- TENGGAT --}}
                        <td class="px-3 py-3">
                            @if($circulation->expected_return_date)
                                <div class="text-xs font-medium {{ $isOverdue ? 'text-red-600' : 'text-gray-800' }} leading-tight">
                                    <i class="fas fa-calendar-alt text-gray-400 text-[10px] mr-1"></i>
                                    {{ $circulation->expected_return_date->format('d/m/Y') }}
                                </div>
                                <div class="text-[10px] {{ $isOverdue ? 'text-red-600 font-bold' : 'text-gray-500' }} mt-0.5">
                                    <i class="fas fa-clock text-[9px] mr-1"></i>
                                    {{ $circulation->expected_return_date->format('H:i') }} WIB
                                </div>

                                @if($isOverdue)
                                    <span class="inline-flex items-center gap-0.5 mt-1 px-1.5 py-0.5 text-[9px] font-bold rounded-full bg-red-100 text-red-700 border border-red-200">
                                        <i class="fas fa-exclamation-triangle text-[8px]"></i>
                                        TERLAMBAT
                                    </span>
                                @elseif($circulation->status == 'approved')
                                    @php
                                        $sisaJam = method_exists($circulation, 'timeUntilDue')
                                            ? $circulation->timeUntilDue()
                                            : null;
                                    @endphp
                                    @if($sisaJam)
                                        <span class="inline-flex items-center gap-0.5 mt-1 px-1.5 py-0.5 text-[9px] font-medium rounded-full bg-green-100 text-green-700 border border-green-200">
                                            <i class="fas fa-hourglass-half text-[8px]"></i>
                                            {{ $sisaJam }}
                                        </span>
                                    @endif
                                @endif
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>

                        {{-- JAM KEMBALI --}}
                        <td class="px-3 py-3">
                            @if($circulation->return_date)
                                <div class="text-xs font-medium text-gray-800 leading-tight">
                                    <i class="fas fa-calendar-check text-green-500 text-[10px] mr-1"></i>
                                    {{ $circulation->return_date->format('d/m/Y') }}
                                </div>
                                <div class="text-[10px] text-gray-500 mt-0.5">
                                    <i class="fas fa-clock text-gray-400 text-[9px] mr-1"></i>
                                    {{ $circulation->return_date->format('H:i') }} WIB
                                </div>
                            @else
                                <span class="text-gray-400 text-[10px] italic">Belum kembali</span>
                            @endif
                        </td>

                        {{-- STATUS --}}
                        <td class="px-3 py-3">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $sc['bg'] }} {{ $sc['text'] }} border {{ $sc['border'] }} whitespace-nowrap">
                                <i class="fas {{ $sc['icon'] }} text-[8px]"></i>
                                {{ $sc['label'] }}
                            </span>

                            {{-- Alasan reject --}}
                            @if($circulation->status == 'rejected' && $circulation->rejection_reason)
                                <div class="mt-1.5 p-1.5 bg-red-50 border-l-2 border-red-400 rounded-r text-[10px] text-red-700">
                                    <p class="break-words leading-snug line-clamp-3">{{ $circulation->rejection_reason }}</p>
                                    @if($circulation->rejected_at)
                                        <p class="mt-0.5 text-[9px] text-red-500/80">
                                            {{ $circulation->rejected_at->format('d/m/Y H:i') }}
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </td>

                        {{-- AKSI --}}
                        <td class="px-3 py-3 text-center">
                            <div class="flex flex-col gap-1.5 items-center">
                                {{-- Detail Barang --}}
                                <a href="{{ route('user.items.show', $circulation->item_id) }}" 
                                   class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-white border border-blue-200 text-blue-600 hover:bg-blue-50 text-[10px] font-medium transition whitespace-nowrap"
                                   title="Detail Barang">
                                    <i class="fas fa-eye text-[9px]"></i>
                                    <span>Detail</span>
                                </a>

                                {{-- Ajukan Pengembalian --}}
                                @if($circulation->status == 'approved')
                                    <form action="{{ route('user.circulations.requestReturn', $circulation) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" 
                                                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-orange-500 hover:bg-orange-600 text-white text-[10px] font-medium transition whitespace-nowrap"
                                                onclick="return confirm('Yakin ingin mengajukan pengembalian barang ini?')">
                                            <i class="fas fa-undo-alt text-[9px]"></i>
                                            <span>Kembali</span>
                                        </button>
                                    </form>
                                @endif

                                {{-- Menunggu Konfirmasi --}}
                                @if($circulation->status == 'return_pending')
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-medium rounded-lg bg-blue-100 text-blue-700 border border-blue-200 whitespace-nowrap"
                                          title="Menunggu konfirmasi admin">
                                        <i class="fas fa-clock text-[9px]"></i>
                                        Menunggu
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                            <div class="w-20 h-20 mx-auto mb-3 rounded-full bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-inbox text-3xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-600 font-medium mb-1">Belum ada riwayat peminjaman</p>
                            <p class="text-gray-400 text-sm mb-4">Silakan ajukan peminjaman barang terlebih dahulu</p>
                            <a href="{{ route('user.items.index') }}" 
                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-xl text-sm font-medium transition shadow-sm hover:shadow-md">
                                <i class="fas fa-plus"></i>
                                Ajukan Peminjaman
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    @if($circulations->hasPages())
    <div class="mt-6">
        {{ $circulations->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection