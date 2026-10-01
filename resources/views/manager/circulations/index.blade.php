@extends('layouts.app')

@section('content')
<div class="container mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-5">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                <i class="fas fa-right-left text-brand-500 mr-2"></i>Monitoring Sirkulasi
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Lihat riwayat peminjaman barang (mode monitoring)
            </p>
        </div>
        <div class="flex items-center gap-2 text-xs sm:text-sm text-gray-500 bg-blue-50 px-3 py-2 rounded-xl border border-blue-100">
            <i class="fas fa-eye text-blue-500"></i>
            <span>Hanya Monitoring</span>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-3 sm:p-4 mb-5">
        <form method="GET" class="flex flex-col sm:flex-row gap-2 sm:gap-3">
            <select name="status"
                    class="w-full sm:w-auto px-4 py-2.5 border-2 border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition">
                <option value="">📋 Semua Status</option>
                <option value="pending"        {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="approved"       {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                <option value="return_pending" {{ request('status') == 'return_pending' ? 'selected' : '' }}>🔄 Return Pending</option>
                <option value="returned"       {{ request('status') == 'returned' ? 'selected' : '' }}>📦 Returned</option>
                <option value="rejected"       {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
            </select>
            <button type="submit"
                    class="w-full sm:w-auto bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-white px-5 py-2.5 rounded-xl transition-all text-sm font-medium shadow-sm hover:shadow-md inline-flex items-center justify-center gap-2">
                <i class="fas fa-filter"></i>
                Filter
            </button>
            @if(request('status'))
                <a href="{{ route('manager.circulations.index') }}"
                   class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2.5 rounded-xl transition inline-flex items-center justify-center"
                   title="Reset filter">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
    </div>

    {{-- ============================================================ --}}
    {{-- 📱 MOBILE VIEW (≤ md) — Card                                --}}
    {{-- ============================================================ --}}
    <div class="block md:hidden space-y-3">
        @forelse($circulations as $circulation)
        @php
            $isOverdue = method_exists($circulation, 'isOverdue') ? ($circulation->isOverdue() ?? false) : false;
            $statusConfig = [
                'pending'        => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200', 'icon' => 'fa-clock',        'label' => 'Menunggu'],
                'approved'       => ['bg' => 'bg-green-100',  'text' => 'text-green-700',  'border' => 'border-green-200',  'icon' => 'fa-check-circle', 'label' => 'Disetujui'],
                'return_pending' => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'border' => 'border-blue-200',   'icon' => 'fa-rotate',       'label' => 'Proses Kembali'],
                'returned'       => ['bg' => 'bg-gray-100',   'text' => 'text-gray-700',   'border' => 'border-gray-200',   'icon' => 'fa-box',          'label' => 'Dikembalikan'],
                'rejected'       => ['bg' => 'bg-red-100',    'text' => 'text-red-700',    'border' => 'border-red-200',    'icon' => 'fa-times-circle', 'label' => 'Ditolak'],
            ];
            $sc = $statusConfig[$circulation->status] ?? $statusConfig['pending'];
        @endphp

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Header: Barang + Status --}}
            <div class="p-3 border-b border-gray-100">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-sm text-gray-800 truncate">{{ $circulation->item->name ?? '-' }}</p>
                        <p class="text-[10px] text-gray-500 mt-0.5">
                            <i class="fas fa-building text-[9px] mr-1"></i>
                            {{ $circulation->item->unit->name ?? '-' }}
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-semibold rounded-full {{ $sc['bg'] }} {{ $sc['text'] }} border {{ $sc['border'] }} shrink-0">
                        <i class="fas {{ $sc['icon'] }} text-[9px]"></i>
                        {{ $sc['label'] }}
                    </span>
                </div>

                {{-- 🔥 Kode stok --}}
                @if($circulation->stock_code)
                    <div class="inline-flex items-start gap-1 px-2 py-1 rounded-md border w-full"
                         style="background: #0F6B5F10; border-color: #0F6B5F30;">
                        <i class="fas fa-qrcode text-[9px] mt-0.5 shrink-0" style="color: #0F6B5F;"></i>
                        <span class="font-mono text-[10px] font-semibold leading-tight break-all"
                              style="color: #0F6B5F;">
                            {{ $circulation->stock_code }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Body --}}
            <div class="grid grid-cols-2 gap-3 p-3 bg-gray-50 border-b border-gray-100">
                <div>
                    <p class="text-[9px] text-gray-500 uppercase font-semibold">Peminjam</p>
                    <p class="text-[11px] font-medium text-gray-800 mt-0.5 truncate">
                        <i class="fas fa-user text-[9px] text-gray-400 mr-1"></i>
                        {{ $circulation->borrower_name }}
                    </p>
                </div>
                <div>
                    <p class="text-[9px] text-gray-500 uppercase font-semibold">Unit</p>
                    <p class="text-[11px] font-medium text-gray-800 mt-0.5 truncate">
                        {{ $circulation->item->unit->name ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-[9px] text-gray-500 uppercase font-semibold">Jam Pinjam</p>
                    <p class="text-[11px] text-gray-700 mt-0.5">
                        {{ $circulation->borrow_date ? $circulation->borrow_date->format('d/m/Y') : '-' }}
                    </p>
                    <p class="text-[10px] text-gray-500">
                        {{ $circulation->borrow_date ? $circulation->borrow_date->format('H:i') : '-' }} WIB
                    </p>
                </div>
                <div>
                    <p class="text-[9px] text-gray-500 uppercase font-semibold">Tenggat</p>
                    @if($circulation->expected_return_date)
                        <p class="text-[11px] font-medium {{ $isOverdue ? 'text-red-600' : 'text-gray-700' }} mt-0.5">
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
                        <p class="text-[11px] text-gray-400 italic mt-0.5">-</p>
                    @endif
                </div>
            </div>

            {{-- Aksi --}}
            <div class="p-2 bg-white">
                <a href="{{ route('manager.circulations.show', $circulation) }}"
                   class="flex items-center justify-center gap-2 py-2.5 rounded-xl text-brand-600 bg-brand-50 hover:bg-brand-100 transition text-xs font-semibold border border-brand-100">
                    <i class="fas fa-eye"></i>
                    <span>Lihat Detail</span>
                </a>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl shadow-sm p-8 text-center border border-gray-100">
            <div class="w-20 h-20 mx-auto mb-3 rounded-full bg-gray-100 flex items-center justify-center">
                <i class="fas fa-inbox text-3xl text-gray-300"></i>
            </div>
            <p class="text-gray-600 font-medium mb-1">Belum ada data sirkulasi</p>
            <p class="text-gray-400 text-xs">Data peminjaman akan muncul di sini</p>
        </div>
        @endforelse
    </div>

    {{-- ============================================================ --}}
    {{-- 💻 TABLET & DESKTOP (≥ md) — Tabel                          --}}
    {{-- ============================================================ --}}
    <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 lg:px-6 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Barang</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Kode Stok</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Peminjam</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Unit</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Jam Pinjam</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Tenggat</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Status</th>
                        <th class="px-4 lg:px-6 py-3 text-center text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($circulations as $circulation)
                    @php
                        $isOverdue = method_exists($circulation, 'isOverdue') ? ($circulation->isOverdue() ?? false) : false;
                        $statusConfig = [
                            'pending'        => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => 'fa-clock',        'label' => 'Menunggu'],
                            'approved'       => ['bg' => 'bg-green-100',  'text' => 'text-green-700',  'icon' => 'fa-check-circle', 'label' => 'Disetujui'],
                            'return_pending' => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'icon' => 'fa-rotate',       'label' => 'Proses Kembali'],
                            'returned'       => ['bg' => 'bg-gray-100',   'text' => 'text-gray-700',   'icon' => 'fa-box',          'label' => 'Dikembalikan'],
                            'rejected'       => ['bg' => 'bg-red-100',    'text' => 'text-red-700',    'icon' => 'fa-times-circle', 'label' => 'Ditolak'],
                        ];
                        $sc = $statusConfig[$circulation->status] ?? $statusConfig['pending'];
                    @endphp
                    <tr class="hover:bg-gray-50/70 transition {{ $isOverdue ? 'bg-red-50/40' : '' }}">
                        {{-- Barang --}}
                        <td class="px-4 lg:px-6 py-3">
                            <p class="text-xs lg:text-sm font-medium text-gray-800">{{ $circulation->item->name ?? '-' }}</p>
                        </td>

                        {{-- Kode Stok --}}
                        <td class="px-4 lg:px-6 py-3 max-w-[220px]">
                            @if($circulation->stock_code)
                                <div class="inline-flex items-start gap-1 px-2 py-1 rounded-md border"
                                     style="background: #0F6B5F10; border-color: #0F6B5F30;">
                                    <i class="fas fa-qrcode text-[9px] mt-0.5 shrink-0" style="color: #0F6B5F;"></i>
                                    <span class="font-mono text-[9px] lg:text-[10px] font-semibold leading-tight break-all"
                                          style="color: #0F6B5F;">
                                        {{ $circulation->stock_code }}
                                    </span>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs italic">-</span>
                            @endif
                        </td>

                        {{-- Peminjam --}}
                        <td class="px-4 lg:px-6 py-3 text-xs lg:text-sm text-gray-700 whitespace-nowrap">
                            {{ $circulation->borrower_name }}
                        </td>

                        {{-- Unit --}}
                        <td class="px-4 lg:px-6 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 text-[10px] font-medium bg-teal-50 text-teal-700 px-2 py-1 rounded-lg border border-teal-100">
                                <i class="fas fa-building text-[9px]"></i>
                                {{ $circulation->item->unit->name ?? '-' }}
                            </span>
                        </td>

                        {{-- Jam Pinjam --}}
                        <td class="px-4 lg:px-6 py-3 whitespace-nowrap">
                            <p class="text-xs lg:text-sm text-gray-800">
                                {{ $circulation->borrow_date ? $circulation->borrow_date->format('d/m/Y') : '-' }}
                            </p>
                            <p class="text-[10px] text-gray-500">
                                {{ $circulation->borrow_date ? $circulation->borrow_date->format('H:i') : '-' }} WIB
                            </p>
                        </td>

                        {{-- Tenggat --}}
                        <td class="px-4 lg:px-6 py-3 whitespace-nowrap">
                            @if($circulation->expected_return_date)
                                <p class="text-xs lg:text-sm {{ $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-800' }}">
                                    {{ $circulation->expected_return_date->format('d/m/Y') }}
                                </p>
                                <p class="text-[10px] {{ $isOverdue ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                    {{ $circulation->expected_return_date->format('H:i') }} WIB
                                </p>
                                @if($isOverdue)
                                    <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 text-[9px] font-bold rounded-full bg-red-100 text-red-700 border border-red-200">
                                        <i class="fas fa-exclamation-triangle text-[8px]"></i>
                                        TERLAMBAT
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-400 text-xs italic">-</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-4 lg:px-6 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] lg:text-xs font-semibold rounded-full {{ $sc['bg'] }} {{ $sc['text'] }}">
                                <i class="fas {{ $sc['icon'] }} text-[9px]"></i>
                                {{ $sc['label'] }}
                            </span>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-4 lg:px-6 py-3 text-center whitespace-nowrap">
                            <a href="{{ route('manager.circulations.show', $circulation) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-600 hover:text-brand-700 transition text-xs font-medium"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                                <span class="hidden lg:inline">Detail</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                    <i class="fas fa-inbox text-3xl text-gray-300"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-600">Belum ada data sirkulasi</p>
                                <p class="text-xs text-gray-400 mt-1">Data peminjaman akan muncul di sini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    @if($circulations->hasPages())
    <div class="mt-5">
        {{ $circulations->withQueryString()->links() }}
    </div>
    @endif

</div>
@endsection