@extends('layouts.app')

@section('title', 'Dashboard')

{{--
    CATATAN UNTUK CONTROLLER (opsional, dashboard tetap jalan tanpa ini):

    Variabel WAJIB (sudah ada sebelumnya, tidak berubah):
        $stats = [
            'total_items'    => ...,
            'total_borrowed' => ...,
            'total_pending'  => ...,
            'total_units'    => ...,
            'total_users'    => ...,
        ];
        $recentItems         -> koleksi Item (code, name, unit->name, location, status)
        $recentCirculations  -> koleksi Circulation (item->name, borrower_name, borrow_date, status, created_at)

    Variabel OPSIONAL (kalau dikirim, dashboard akan menampilkan data asli, bukan state kosong):
        $stats['total_maintenance']  -> jumlah barang status 'maintenance'
        $stats['total_nilai_aset']   -> total nilai aset (harga × stok semua barang)
        $chartTrend = [
            'labels' => ['Jan','Feb','Mar','Apr','Mei','Jun'],
            'data'   => [12, 19, 14, 22, 18, 25],
        ];
        $unitBreakdown = [
            ['name' => 'SMPIT', 'count' => 120],
            ['name' => 'Daycare', 'count' => 80],
            ...
        ];
--}}

@section('content')
@php
    $totalItems    = $stats['total_items'] ?? 0;
    $totalBorrowed = $stats['total_borrowed'] ?? 0;
    $totalPending  = $stats['total_pending'] ?? 0;
    $totalUnits    = $stats['total_units'] ?? 0;
    $totalUsers    = $stats['total_users'] ?? 0;

    // 🔥 TOTAL NILAI ASET — fallback hitung di blade kalau controller belum kirim
    if (isset($stats['total_nilai_aset'])) {
        $totalNilaiAset = (float) $stats['total_nilai_aset'];
    } else {
        $totalNilaiAset = 0.0;
        if (class_exists(\App\Models\Item::class)) {
            foreach (\App\Models\Item::all() as $it) {
                $h    = (float) ($it->price ?? 0);
                $s    = (int)   ($it->stock ?? 1);
                $totalNilaiAset += $h * $s;
            }
        }
    }

    $totalAvailable = max($totalItems - $totalBorrowed, 0);

    $trendLabels = $chartTrend['labels'] ?? ['Jan','Feb','Mar','Apr','Mei','Jun'];
    $trendData   = $chartTrend['data'] ?? array_fill(0, count($trendLabels), 0);
    $hasTrendData = isset($chartTrend);

    $hasUnitBreakdown = isset($unitBreakdown) && count($unitBreakdown) > 0;
@endphp

{{-- ============================================================ --}}
{{-- 🔥 CUSTOM STYLE — Animasi khusus card nilai aset             --}}
{{-- ============================================================ --}}
@push('styles')
<style>
    /* Gradient border berputar */
    @keyframes borderSpin {
        0%   { background-position: 0% 50%; }
        50%  { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .asset-card {
        position: relative;
        border-radius: 18px;
        padding: 2px;
        background: linear-gradient(90deg, #f59e0b, #fbbf24, #fcd34d, #fbbf24, #f59e0b);
        background-size: 300% 100%;
        animation: borderSpin 6s ease-in-out infinite;
        box-shadow: 0 10px 30px -10px rgba(245, 158, 11, 0.35);
        transition: transform 0.4s ease, box-shadow 0.4s ease;
    }

    .asset-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 40px -12px rgba(245, 158, 11, 0.5);
    }

    /* Shine effect (kilau melintas) */
    .asset-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 60%;
        height: 100%;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(255, 255, 255, 0.35),
            transparent
        );
        border-radius: 18px;
        animation: shine 4s ease-in-out infinite;
        pointer-events: none;
        z-index: 20;
    }

    @keyframes shine {
        0%   { left: -100%; }
        50%  { left: 150%; }
        100% { left: 150%; }
    }

    /* Ikon koin — pulse */
    .coin-icon {
        animation: coinPulse 3s ease-in-out infinite;
    }

    @keyframes coinPulse {
        0%, 100% { transform: scale(1) rotate(0deg); }
        50%      { transform: scale(1.06) rotate(-5deg); }
    }

    /* Angka nilai aset — slide in dari bawah */
    @keyframes countUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .asset-value {
        animation: countUp 0.9s ease-out 0.2s both;
    }

    /* Tombol Lihat — hover lebih hidup */
    .btn-asset {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .btn-asset::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.25), transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .btn-asset:hover::before {
        opacity: 1;
    }

    .btn-asset:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px -8px rgba(245, 158, 11, 0.6);
    }

    .btn-asset i.fa-arrow-right {
        transition: transform 0.3s ease;
    }

    .btn-asset:hover i.fa-arrow-right {
        transform: translateX(4px);
    }

    /* Dekorasi lingkaran */
    .deco-circle {
        animation: floatCircle 8s ease-in-out infinite;
    }

    @keyframes floatCircle {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50%      { transform: translate(-10px, 10px) scale(1.1); }
    }
</style>
@endpush

<div class="max-w-[1600px] mx-auto">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-5 sm:mb-6 animate-fadeInUp">
        <div>
            <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800">
                Selamat datang kembali, <span class="text-brand-600">{{ Auth::user()->name }}!</span>
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Berikut ringkasan inventaris Yayasan Permata</p>
        </div>
        <div class="flex items-center gap-2 bg-white border border-gray-100 shadow-sm rounded-xl px-3 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm text-gray-600 w-fit">
            <i class="fas fa-calendar-days text-brand-500"></i>
            {{ now()->translatedFormat('d F Y') }}
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 🔥 CARD TOTAL NILAI ASET — Versi Baru Lebih Rapi & Kecil     --}}
    {{-- ============================================================ --}}
    <div class="mb-5 sm:mb-6 animate-fadeInUp">
        <div class="asset-card">
            <div class="relative rounded-[16px] bg-white overflow-hidden">
                {{-- Dekorasi background --}}
                <div class="absolute top-0 right-0 w-40 sm:w-56 h-40 sm:h-56 bg-gradient-to-br from-amber-100/70 to-transparent rounded-full -translate-y-1/2 translate-x-1/2 pointer-events-none deco-circle"></div>
                <div class="absolute bottom-0 left-0 w-24 sm:w-32 h-24 sm:h-32 bg-gradient-to-tr from-amber-100/50 to-transparent rounded-full translate-y-1/2 -translate-x-1/2 pointer-events-none"></div>

                <div class="relative p-4 sm:p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">

                        {{-- KIRI: Ikon + Label + Nilai --}}
                        <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                            {{-- Ikon koin --}}
                            <div class="coin-icon w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/30 shrink-0">
                                <i class="fas fa-coins text-white text-xl sm:text-2xl"></i>
                            </div>

                            {{-- Label + Nilai --}}
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] sm:text-xs font-bold text-amber-700 uppercase tracking-wider">
                                    <i class="fas fa-gem mr-1"></i>Total Nilai Aset
                                </p>
                                <p class="asset-value text-lg sm:text-2xl md:text-3xl font-extrabold text-amber-600 tracking-tight mt-0.5 leading-tight truncate">
                                    Rp {{ number_format((float) $totalNilaiAset, 0, ',', '.') }}
                                </p>
                                <p class="text-[10px] sm:text-xs text-gray-500 mt-0.5 hidden sm:block">
                                    <i class="fas fa-info-circle mr-1 text-amber-400"></i>
                                    Total kekayaan inventaris
                                </p>
                            </div>
                        </div>

                        {{-- KANAN: Tombol Lihat --}}
                        <a href="{{ route('super_admin.assets.index') }}"
                           class="btn-asset inline-flex items-center justify-center gap-2 px-4 sm:px-5 py-2.5 sm:py-3 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-white font-semibold text-xs sm:text-sm shadow-lg shadow-amber-500/30 shrink-0 w-full sm:w-auto">
                            <i class="fas fa-eye"></i>
                            <span>Lihat Detail</span>
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-5 sm:mb-6 stagger">
        <div class="card-elevated p-4 sm:p-5">
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center mb-3 shadow-brand">
                <i class="fas fa-boxes-stacked text-white text-sm sm:text-base"></i>
            </div>
            <p class="text-xs sm:text-sm text-gray-500">Total Barang</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-800 mt-0.5 tracking-tight">{{ number_format($totalItems) }}</p>
        </div>

        <div class="card-elevated p-4 sm:p-5">
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center mb-3 shadow-[0_10px_30px_-10px_rgba(217,119,6,0.35)]">
                <i class="fas fa-hand-holding text-white text-sm sm:text-base"></i>
            </div>
            <p class="text-xs sm:text-sm text-gray-500">Dipinjam</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-800 mt-0.5 tracking-tight">{{ number_format($totalBorrowed) }}</p>
        </div>

        <div class="card-elevated p-4 sm:p-5">
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center mb-3 shadow-[0_10px_30px_-10px_rgba(220,38,38,0.35)]">
                <i class="fas fa-clock text-white text-sm sm:text-base"></i>
            </div>
            <p class="text-xs sm:text-sm text-gray-500">Pending</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-800 mt-0.5 tracking-tight">{{ number_format($totalPending) }}</p>
        </div>

        <div class="card-elevated p-4 sm:p-5">
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center mb-3 shadow-[0_10px_30px_-10px_rgba(5,150,105,0.35)]">
                <i class="fas fa-building text-white text-sm sm:text-base"></i>
            </div>
            <p class="text-xs sm:text-sm text-gray-500">Total Unit</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-800 mt-0.5 tracking-tight">{{ number_format($totalUnits) }}</p>
        </div>

        <div class="card-elevated p-4 sm:p-5">
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center mb-3 shadow-[0_10px_30px_-10px_rgba(147,51,234,0.35)]">
                <i class="fas fa-users text-white text-sm sm:text-base"></i>
            </div>
            <p class="text-xs sm:text-sm text-gray-500">Total User</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-800 mt-0.5 tracking-tight">{{ number_format($totalUsers) }}</p>
        </div>
    </div>

    {{-- CHARTS + ACTIVITY --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-5 sm:mb-6">

        {{-- TREN PEMINJAMAN --}}
        <div class="lg:col-span-1 card-elevated p-4 sm:p-5 animate-fadeInUp" style="animation-delay:.1s">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2 text-sm sm:text-base">
                    <span class="w-7 h-7 rounded-lg bg-brand-50 flex items-center justify-center">
                        <i class="fas fa-chart-line text-brand-600 text-[11px]"></i>
                    </span>
                    Tren Peminjaman
                </h3>
                <span class="text-[10px] sm:text-xs text-gray-400">6 Bulan Terakhir</span>
            </div>
            @if($hasTrendData)
                <div class="chart-fallback-slot" style="height:200px">
                    <canvas id="trendChart" width="600" height="220"></canvas>
                </div>
            @else
                <div class="h-[180px] flex flex-col items-center justify-center text-center rounded-xl bg-gradient-to-b from-brand-50/60 to-transparent">
                    <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center mb-3">
                        <i class="fas fa-chart-line text-brand-400"></i>
                    </div>
                    <p class="text-xs text-gray-400 max-w-[190px]">Grafik tren akan tampil otomatis setelah data peminjaman tersambung.</p>
                </div>
            @endif
        </div>

        {{-- STATUS BARANG --}}
        @php
            $statusParts = [
                ['label' => 'Tersedia',  'value' => $totalAvailable, 'color' => '#10b981', 'bar' => 'bg-emerald-500'],
                ['label' => 'Dipinjam',  'value' => $totalBorrowed,  'color' => '#149c8c', 'bar' => 'bg-brand-500'],
                ['label' => 'Pending',   'value' => $totalPending,   'color' => '#f87171', 'bar' => 'bg-red-400'],
            ];
        @endphp
        <div class="lg:col-span-1 card-elevated p-4 sm:p-5 animate-fadeInUp" style="animation-delay:.18s">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-semibold text-gray-800 text-sm sm:text-base">Status Barang</h3>
                <span class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center">
                    <i class="fas fa-chart-pie text-brand-600 text-xs"></i>
                </span>
            </div>

            <div class="flex items-center justify-center mb-5 chart-fallback-slot">
                <div class="relative shrink-0" style="width:150px;height:150px">
                    <canvas id="statusChart" width="160" height="160"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-xl sm:text-2xl font-extrabold text-gray-800 tracking-tight">{{ number_format($totalItems) }}</span>
                        <span class="text-[10px] sm:text-[11px] text-gray-400 tracking-wide">Total Barang</span>
                    </div>
                </div>
            </div>

            <ul class="space-y-3">
                @foreach($statusParts as $part)
                    @php $pct = $totalItems > 0 ? round(($part['value'] / $totalItems) * 100) : 0; @endphp
                    <li>
                        <div class="flex items-center justify-between text-xs sm:text-sm mb-1">
                            <span class="flex items-center gap-2 text-gray-600">
                                <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $part['color'] }}"></span>
                                {{ $part['label'] }}
                            </span>
                            <span class="font-semibold text-gray-800">{{ number_format($part['value']) }} <span class="text-gray-400 font-normal">({{ $pct }}%)</span></span>
                        </div>
                        <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full rounded-full {{ $part['bar'] }} transition-all duration-700 ease-out" style="width: 0%" data-target-width="{{ $pct }}%"></div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- AKTIVITAS TERBARU --}}
        <div class="md:col-span-2 lg:col-span-1 card-elevated p-4 sm:p-5 animate-fadeInUp" style="animation-delay:.26s">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2 text-sm sm:text-base">
                    <span class="w-7 h-7 rounded-lg bg-brand-50 flex items-center justify-center">
                        <i class="fas fa-bolt text-brand-600 text-[11px]"></i>
                    </span>
                    Aktivitas Terbaru
                </h3>
                <a href="{{ route('super_admin.circulations.index') }}" class="text-[10px] sm:text-xs text-brand-600 hover:text-brand-800 font-medium">Lihat semua</a>
            </div>
            <ul class="divide-y divide-gray-50 max-h-[260px] overflow-y-auto thin-scroll">
                @forelse($recentCirculations as $circulation)
                    @php
                        $statusMap = [
                            'approved' => ['icon' => 'fa-check', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'label' => 'dipinjam'],
                            'pending'  => ['icon' => 'fa-clock', 'bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'label' => 'menunggu persetujuan'],
                            'returned' => ['icon' => 'fa-rotate-left', 'bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'label' => 'dikembalikan'],
                        ];
                        $meta = $statusMap[$circulation->status] ?? ['icon' => 'fa-xmark', 'bg' => 'bg-red-50', 'text' => 'text-red-600', 'label' => $circulation->status];
                    @endphp
                    <li class="flex items-start gap-3 py-3">
                        <div class="w-9 h-9 rounded-lg {{ $meta['bg'] }} flex items-center justify-center shrink-0">
                            <i class="fas {{ $meta['icon'] }} {{ $meta['text'] }} text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs sm:text-sm text-gray-700 truncate">
                                <span class="font-medium">{{ $circulation->item->name }}</span>
                                {{ $meta['label'] }} oleh
                                <span class="font-medium">{{ $circulation->borrower_name }}</span>
                            </p>
                            <p class="text-[10px] sm:text-xs text-gray-400 mt-0.5">
                                {{ ($circulation->created_at ?? $circulation->borrow_date)->diffForHumans() }}
                            </p>
                        </div>
                    </li>
                @empty
                    <li class="py-8 text-center text-gray-400 text-sm">
                        <i class="fas fa-inbox text-2xl block mb-2"></i>
                        Belum ada aktivitas.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- TABLE + QUICK ACTIONS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 sm:gap-4">

        {{-- BARANG TERBARU --}}
        <div class="lg:col-span-2 card-elevated animate-fadeInUp" style="animation-delay:.1s">
            <div class="flex items-center justify-between px-4 sm:px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2 text-sm sm:text-base">
                    <span class="w-7 h-7 rounded-lg bg-brand-50 flex items-center justify-center">
                        <i class="fas fa-boxes-stacked text-brand-600 text-[11px]"></i>
                    </span>
                    Barang Terbaru
                </h3>
                <a href="{{ route('super_admin.items.index') }}"
                   class="text-[10px] sm:text-xs font-medium bg-brand-50 text-brand-700 hover:bg-brand-100 px-3 py-1.5 rounded-lg transition">
                    Lihat Semua
                </a>
            </div>
            <div class="overflow-x-auto">
                @if($recentItems->count() > 0)
                <table class="min-w-full text-xs sm:text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 sm:px-5 py-3 text-left text-[10px] sm:text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Kode</th>
                            <th class="px-4 sm:px-5 py-3 text-left text-[10px] sm:text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Nama Barang</th>
                            <th class="px-4 sm:px-5 py-3 text-left text-[10px] sm:text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Unit</th>
                            <th class="px-4 sm:px-5 py-3 text-left text-[10px] sm:text-[11px] font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Lokasi</th>
                            <th class="px-4 sm:px-5 py-3 text-left text-[10px] sm:text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                            <th class="px-4 sm:px-5 py-3 text-left text-[10px] sm:text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($recentItems as $item)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="px-4 sm:px-5 py-3.5 font-medium text-gray-700">{{ $item->code }}</td>
                            <td class="px-4 sm:px-5 py-3.5 text-gray-700">{{ $item->name }}</td>
                            <td class="px-4 sm:px-5 py-3.5 text-gray-500">{{ $item->unit->name ?? '-' }}</td>
                            <td class="px-4 sm:px-5 py-3.5 text-gray-500 hidden md:table-cell">{{ $item->location }}</td>
                            <td class="px-4 sm:px-5 py-3.5">
                                <span class="px-2 sm:px-2.5 py-1 text-[10px] sm:text-xs font-medium rounded-full
                                    {{ $item->status == 'available' ? 'bg-emerald-50 text-emerald-700' :
                                       ($item->status == 'borrowed' ? 'bg-brand-50 text-brand-700' : 'bg-amber-50 text-amber-700') }}">
                                    {{ $item->status == 'available' ? 'Tersedia' : ($item->status == 'borrowed' ? 'Dipinjam' : 'Perbaikan') }}
                                </span>
                            </td>
                            <td class="px-4 sm:px-5 py-3.5">
                                <div class="flex items-center gap-3 text-gray-400">
                                    <a href="{{ route('super_admin.items.show', $item) }}" class="hover:text-brand-600" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('super_admin.items.edit', $item) }}" class="hover:text-amber-600" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('super_admin.items.destroy', $item) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="hover:text-red-600" title="Hapus" onclick="return confirm('Yakin ingin menghapus barang ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center py-12 text-gray-400">
                    <i class="fas fa-box-open text-4xl mb-2 block"></i>
                    <p class="text-sm">Belum ada data barang</p>
                </div>
                @endif
            </div>
        </div>

        {{-- QUICK ACTION + RINGKASAN UNIT --}}
        <div class="lg:col-span-1 space-y-3 sm:space-y-4">

            <div class="card-elevated p-4 sm:p-5 animate-fadeInUp" style="animation-delay:.18s">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2 text-sm sm:text-base">
                    <span class="w-7 h-7 rounded-lg bg-brand-50 flex items-center justify-center">
                        <i class="fas fa-bolt-lightning text-brand-600 text-[11px]"></i>
                    </span>
                    Quick Action
                </h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('super_admin.items.create') }}"
                       class="flex flex-col items-center justify-center gap-2 rounded-xl bg-brand-50 hover:bg-brand-100 text-brand-700 py-4 transition">
                        <i class="fas fa-plus text-lg"></i>
                        <span class="text-xs font-medium text-center">Tambah Barang</span>
                    </a>
                    <a href="{{ route('super_admin.circulations.index') }}"
                       class="flex flex-col items-center justify-center gap-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 py-4 transition">
                        <i class="fas fa-right-left text-lg"></i>
                        <span class="text-xs font-medium text-center">Kelola Sirkulasi</span>
                    </a>
                    <a href="{{ route('super_admin.units.create') }}"
                       class="flex flex-col items-center justify-center gap-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 py-4 transition">
                        <i class="fas fa-building text-lg"></i>
                        <span class="text-xs font-medium text-center">Tambah Unit</span>
                    </a>
                    <a href="{{ route('super_admin.users.index') }}"
                       class="flex flex-col items-center justify-center gap-2 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 py-4 transition">
                        <i class="fas fa-user-gear text-lg"></i>
                        <span class="text-xs font-medium text-center">Manajemen Akun</span>
                    </a>
                </div>
            </div>

            <div class="card-elevated p-4 sm:p-5 animate-fadeInUp" style="animation-delay:.26s">
                <h3 class="font-semibold text-gray-800 mb-4 text-sm sm:text-base">Ringkasan per Unit</h3>
                @if($hasUnitBreakdown)
                    <div class="flex items-center gap-5">
                        <div class="relative shrink-0 chart-fallback-slot" style="width:128px;height:128px">
                            <canvas id="unitChart" width="128" height="128"></canvas>
                        </div>
                        <ul class="space-y-2 text-xs sm:text-sm flex-1 min-w-0">
                            @foreach($unitBreakdown as $i => $u)
                                <li class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ ['#22988e','#3fb3a9','#a6e6df','#134e4a','#71d1c8'][$i % 5] }}"></span>
                                    <span class="text-gray-600 truncate">{{ $u['name'] }}</span>
                                    <span class="ml-auto font-semibold text-gray-800">{{ $u['count'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="text-center py-5 rounded-xl bg-gradient-to-b from-brand-50/60 to-transparent">
                        <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-building text-brand-400"></i>
                        </div>
                        <p class="text-xs text-gray-400 max-w-[190px] mx-auto">Ringkasan per unit akan tampil otomatis setelah data unit tersambung.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1) Animasi progress bar status barang
    document.querySelectorAll('[data-target-width]').forEach((bar, i) => {
        setTimeout(() => { bar.style.width = bar.dataset.targetWidth; }, 300 + (i * 150));
    });

    // 2) Kalau Chart.js gagal dimuat
    if (typeof Chart === 'undefined') {
        console.error('Chart.js tidak berhasil dimuat dari CDN.');
        document.querySelectorAll('.chart-fallback-slot').forEach(function (slot) {
            slot.innerHTML = '<div class="text-center text-red-500 text-xs py-6">' +
                '<i class="fas fa-triangle-exclamation text-lg mb-1 block"></i>' +
                'Chart.js gagal dimuat.<br>Cek koneksi internet, lalu refresh halaman.</div>';
        });
        return;
    }

    setTimeout(initCharts, 60);

    function initCharts() {
    const brand = { solid: '#149c8c', dark: '#0d6459', light: '#9deadb', pending: '#f87171', emerald: '#10b981' };

    @if($hasTrendData)
    try {
        const trendCtx = document.getElementById('trendChart');
        if (trendCtx) {
            const gradientFill = trendCtx.getContext('2d').createLinearGradient(0, 0, 0, 220);
            gradientFill.addColorStop(0, 'rgba(20,156,140,0.35)');
            gradientFill.addColorStop(1, 'rgba(20,156,140,0.02)');

            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: @json($trendLabels),
                    datasets: [{
                        label: 'Peminjaman',
                        data: @json($trendData),
                        borderColor: brand.solid,
                        backgroundColor: gradientFill,
                        fill: true,
                        tension: 0.45,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: brand.solid,
                        pointBorderWidth: 2,
                        pointHoverBackgroundColor: brand.solid,
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 3,
                        borderWidth: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 1300, easing: 'easeOutQuart' },
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: brand.dark,
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            padding: 10,
                            cornerRadius: 10,
                            displayColors: false,
                            titleFont: { weight: '600' },
                            callbacks: {
                                label: (ctx) => ' ' + ctx.parsed.y + ' peminjaman'
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0 } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    } catch (err) {
        console.error('Gagal membuat trendChart:', err);
    }
    @endif

    @php
        $statusLabels = ['Tersedia', 'Dipinjam', 'Pending'];
        $statusValues = [$totalAvailable, $totalBorrowed, $totalPending];
    @endphp
    try {
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($statusLabels),
                    datasets: [{
                        data: @json($statusValues),
                        backgroundColor: [brand.emerald, brand.solid, brand.pending],
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        borderRadius: 6,
                        hoverOffset: 6,
                        spacing: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '74%',
                    radius: '92%',
                    animation: { duration: 1200, easing: 'easeOutQuart' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: brand.dark,
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            padding: 10,
                            cornerRadius: 10,
                            displayColors: true,
                            boxPadding: 4,
                        }
                    }
                }
            });
        }
    } catch (err) {
        console.error('Gagal membuat statusChart:', err);
    }

    @if($hasUnitBreakdown)
    try {
        const unitCtx = document.getElementById('unitChart');
        if (unitCtx) {
            new Chart(unitCtx, {
                type: 'doughnut',
                data: {
                    labels: @json(collect($unitBreakdown)->pluck('name')),
                    datasets: [{
                        data: @json(collect($unitBreakdown)->pluck('count')),
                        backgroundColor: ['#149c8c','#39c1ad','#9deadb','#0d6459','#68d9c7'],
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        borderRadius: 6,
                        hoverOffset: 6,
                        spacing: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    radius: '92%',
                    animation: { duration: 1200, easing: 'easeOutQuart' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: brand.dark,
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            padding: 10,
                            cornerRadius: 10,
                            displayColors: true,
                            boxPadding: 4,
                        }
                    }
                }
            });
        }
    } catch (err) {
        console.error('Gagal membuat unitChart:', err);
    }
    @endif
    } // end initCharts
});
</script>
@endpush
@endsection