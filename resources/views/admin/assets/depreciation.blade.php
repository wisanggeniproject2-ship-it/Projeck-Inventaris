@extends('layouts.app')

@section('content')
<div class="container mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-5 sm:mb-6">
        <div>
            <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">
                <i class="fas fa-chart-line text-brand-500 mr-2"></i>Penyusutan Aset
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Pantau penyusutan inventaris — per unit & total keseluruhan</p>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 🔥 GRAND TOTAL — SEMUA UNIT                                  --}}
    {{-- ============================================================ --}}
    <div class="mb-5 sm:mb-6">
        <h2 class="text-xs sm:text-sm font-bold text-gray-600 uppercase tracking-wide mb-3 flex items-center gap-2">
            <i class="fas fa-layer-group text-brand-500"></i>
            Total Semua Unit
        </h2>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3 lg:gap-4">
            {{-- Total Nilai Aset (Harga × Stok) --}}
            <div class="bg-gradient-to-br from-blue-50 to-white border-2 border-blue-100 rounded-xl p-3 sm:p-4 lg:p-5">
                <div class="flex items-center gap-2 sm:gap-3 mb-2">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-blue-500 flex items-center justify-center shrink-0">
                        <i class="fas fa-coins text-white text-xs sm:text-sm"></i>
                    </div>
                    <span class="text-[9px] sm:text-[10px] lg:text-xs font-semibold text-blue-700 uppercase leading-tight">Total Nilai Aset</span>
                </div>
                <p class="text-sm sm:text-base lg:text-xl font-bold text-blue-600 break-all">
                    Rp {{ number_format($grandTotalNilai, 0, ',', '.') }}
                </p>
                <p class="text-[9px] sm:text-[10px] lg:text-xs text-gray-500 mt-1 hidden sm:block">Harga asli × stok</p>
            </div>

            {{-- Akumulasi --}}
            <div class="bg-gradient-to-br from-red-50 to-white border-2 border-red-100 rounded-xl p-3 sm:p-4 lg:p-5">
                <div class="flex items-center gap-2 sm:gap-3 mb-2">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-red-500 flex items-center justify-center shrink-0">
                        <i class="fas fa-arrow-trend-down text-white text-xs sm:text-sm"></i>
                    </div>
                    <span class="text-[9px] sm:text-[10px] lg:text-xs font-semibold text-red-700 uppercase leading-tight">Akumulasi</span>
                </div>
                <p class="text-sm sm:text-base lg:text-xl font-bold text-red-600 break-all">
                    Rp {{ number_format($grandTotalAkum, 0, ',', '.') }}
                </p>
                <p class="text-[9px] sm:text-[10px] lg:text-xs text-gray-500 mt-1 hidden sm:block">Penyusutan berjalan</p>
            </div>

            {{-- Nilai Buku --}}
            <div class="bg-gradient-to-br from-green-50 to-white border-2 border-green-100 rounded-xl p-3 sm:p-4 lg:p-5">
                <div class="flex items-center gap-2 sm:gap-3 mb-2">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-green-500 flex items-center justify-center shrink-0">
                        <i class="fas fa-wallet text-white text-xs sm:text-sm"></i>
                    </div>
                    <span class="text-[9px] sm:text-[10px] lg:text-xs font-semibold text-green-700 uppercase leading-tight">Nilai Buku</span>
                </div>
                <p class="text-sm sm:text-base lg:text-xl font-bold text-green-600 break-all">
                    Rp {{ number_format($grandTotalBuku, 0, ',', '.') }}
                </p>
                <p class="text-[9px] sm:text-[10px] lg:text-xs text-gray-500 mt-1 hidden sm:block">Nilai saat ini</p>
            </div>

            {{-- Total Barang --}}
            <div class="bg-gradient-to-br from-purple-50 to-white border-2 border-purple-100 rounded-xl p-3 sm:p-4 lg:p-5">
                <div class="flex items-center gap-2 sm:gap-3 mb-2">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-purple-500 flex items-center justify-center shrink-0">
                        <i class="fas fa-boxes-stacked text-white text-xs sm:text-sm"></i>
                    </div>
                    <span class="text-[9px] sm:text-[10px] lg:text-xs font-semibold text-purple-700 uppercase leading-tight">Total Barang</span>
                </div>
                <p class="text-sm sm:text-base lg:text-xl font-bold text-purple-600">
                    {{ number_format($grandTotalBarang) }} unit
                </p>
                <p class="text-[9px] sm:text-[10px] lg:text-xs text-gray-500 mt-1 hidden sm:block">{{ count($unitSummary) }} unit sekolah</p>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 🔥 RINGKASAN PER UNIT                                         --}}
    {{-- ============================================================ --}}
    <div class="mb-5 sm:mb-6 bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <div class="px-4 sm:px-5 py-3 sm:py-4 border-b border-gray-100 flex items-center gap-2 bg-gradient-to-r from-gray-50 to-white">
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-brand-50 flex items-center justify-center shrink-0">
                <i class="fas fa-building text-brand-600 text-xs sm:text-sm"></i>
            </div>
            <div class="min-w-0">
                <h3 class="font-bold text-gray-800 text-xs sm:text-sm lg:text-base">Ringkasan Per Unit</h3>
                <p class="text-[10px] sm:text-xs text-gray-500">Total nilai aset & penyusutan tiap unit</p>
            </div>
        </div>

        {{-- 📱 MOBILE VIEW: Card per unit --}}
        <div class="block md:hidden divide-y divide-gray-100">
            @foreach($unitSummary as $unitName => $summary)
            @php
                $pctPenyusutan = $summary['total_nilai'] > 0
                    ? round(($summary['total_akumulasi'] / $summary['total_nilai']) * 100, 1)
                    : 0;
            @endphp
            <div class="p-4">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-brand-100 flex items-center justify-center shrink-0">
                            <i class="fas fa-building text-brand-600 text-xs"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-sm text-gray-800 truncate">{{ $unitName }}</p>
                            <p class="text-[10px] text-gray-500">{{ $summary['jumlah_item'] }} jenis • {{ number_format($summary['total_barang']) }} stok</p>
                        </div>
                    </div>
                    <span class="inline-block px-2 py-1 text-[10px] font-bold rounded-full shrink-0
                        {{ $pctPenyusutan >= 100 ? 'bg-red-100 text-red-700' : ($pctPenyusutan >= 75 ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700') }}">
                        {{ $pctPenyusutan }}%
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="bg-blue-50 rounded-lg p-2">
                        <p class="text-[9px] font-semibold text-blue-700 uppercase">Nilai Aset</p>
                        <p class="text-[10px] font-bold text-blue-600 break-all mt-0.5">
                            Rp {{ number_format($summary['total_nilai'], 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-red-50 rounded-lg p-2">
                        <p class="text-[9px] font-semibold text-red-700 uppercase">Penyusutan</p>
                        <p class="text-[10px] font-bold text-red-600 break-all mt-0.5">
                            Rp {{ number_format($summary['total_akumulasi'], 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-2">
                        <p class="text-[9px] font-semibold text-green-700 uppercase">Nilai Buku</p>
                        <p class="text-[10px] font-bold text-green-600 break-all mt-0.5">
                            Rp {{ number_format($summary['total_buku'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="bg-gradient-to-r from-brand-500 to-brand-600 p-4">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-calculator text-white text-sm"></i>
                        <span class="font-bold text-white text-sm uppercase">TOTAL SEMUA</span>
                    </div>
                    @php
                        $pctTotal = $grandTotalNilai > 0
                            ? round(($grandTotalAkum / $grandTotalNilai) * 100, 1)
                            : 0;
                    @endphp
                    <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-white/20 text-white">
                        {{ $pctTotal }}%
                    </span>
                </div>
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="bg-white/15 rounded-lg p-2">
                        <p class="text-[9px] font-semibold text-white/80 uppercase">Nilai Aset</p>
                        <p class="text-[10px] font-bold text-white break-all mt-0.5">
                            Rp {{ number_format($grandTotalNilai, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-white/15 rounded-lg p-2">
                        <p class="text-[9px] font-semibold text-white/80 uppercase">Penyusutan</p>
                        <p class="text-[10px] font-bold text-white break-all mt-0.5">
                            Rp {{ number_format($grandTotalAkum, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-white/15 rounded-lg p-2">
                        <p class="text-[9px] font-semibold text-white/80 uppercase">Nilai Buku</p>
                        <p class="text-[10px] font-bold text-white break-all mt-0.5">
                            Rp {{ number_format($grandTotalBuku, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 💻 TABLET & DESKTOP: Tabel --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 lg:px-4 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase">Unit</th>
                        <th class="px-3 lg:px-4 py-3 text-center text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase">Jenis</th>
                        <th class="px-3 lg:px-4 py-3 text-center text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase">Stok</th>
                        <th class="px-3 lg:px-4 py-3 text-right text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase">Nilai Aset</th>
                        <th class="px-3 lg:px-4 py-3 text-right text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase">Penyusutan</th>
                        <th class="px-3 lg:px-4 py-3 text-right text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase">Nilai Buku</th>
                        <th class="px-3 lg:px-4 py-3 text-center text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($unitSummary as $unitName => $summary)
                    @php
                        $pctPenyusutan = $summary['total_nilai'] > 0
                            ? round(($summary['total_akumulasi'] / $summary['total_nilai']) * 100, 1)
                            : 0;
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-3 lg:px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 lg:w-8 lg:h-8 rounded-lg bg-brand-100 flex items-center justify-center shrink-0">
                                    <i class="fas fa-building text-brand-600 text-[10px] lg:text-xs"></i>
                                </div>
                                <span class="font-semibold text-xs lg:text-sm text-gray-800">{{ $unitName }}</span>
                            </div>
                        </td>
                        <td class="px-3 lg:px-4 py-3 text-center text-xs lg:text-sm text-gray-700">
                            {{ $summary['jumlah_item'] }}
                        </td>
                        <td class="px-3 lg:px-4 py-3 text-center text-xs lg:text-sm text-gray-700">
                            {{ number_format($summary['total_barang']) }}
                        </td>
                        <td class="px-3 lg:px-4 py-3 text-right text-xs lg:text-sm text-blue-600 font-semibold whitespace-nowrap">
                            Rp {{ number_format($summary['total_nilai'], 0, ',', '.') }}
                        </td>
                        <td class="px-3 lg:px-4 py-3 text-right text-xs lg:text-sm text-red-600 font-semibold whitespace-nowrap">
                            Rp {{ number_format($summary['total_akumulasi'], 0, ',', '.') }}
                        </td>
                        <td class="px-3 lg:px-4 py-3 text-right text-xs lg:text-sm text-green-600 font-bold whitespace-nowrap">
                            Rp {{ number_format($summary['total_buku'], 0, ',', '.') }}
                        </td>
                        <td class="px-3 lg:px-4 py-3 text-center">
                            <span class="inline-block px-2 py-1 text-[10px] lg:text-xs font-bold rounded-full
                                {{ $pctPenyusutan >= 100 ? 'bg-red-100 text-red-700' : ($pctPenyusutan >= 75 ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700') }}">
                                {{ $pctPenyusutan }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

                <tfoot class="bg-gradient-to-r from-brand-500 to-brand-600">
                    <tr>
                        <td class="px-3 lg:px-4 py-3 lg:py-4 font-bold text-white text-xs lg:text-sm uppercase whitespace-nowrap">
                            <i class="fas fa-calculator mr-1"></i>
                            TOTAL
                        </td>
                        <td class="px-3 lg:px-4 py-3 lg:py-4 text-center font-bold text-white text-xs lg:text-sm">
                            {{ collect($unitSummary)->sum('jumlah_item') }}
                        </td>
                        <td class="px-3 lg:px-4 py-3 lg:py-4 text-center font-bold text-white text-xs lg:text-sm">
                            {{ number_format($grandTotalBarang) }}
                        </td>
                        <td class="px-3 lg:px-4 py-3 lg:py-4 text-right font-bold text-white text-xs lg:text-sm whitespace-nowrap">
                            Rp {{ number_format($grandTotalNilai, 0, ',', '.') }}
                        </td>
                        <td class="px-3 lg:px-4 py-3 lg:py-4 text-right font-bold text-white text-xs lg:text-sm whitespace-nowrap">
                            Rp {{ number_format($grandTotalAkum, 0, ',', '.') }}
                        </td>
                        <td class="px-3 lg:px-4 py-3 lg:py-4 text-right font-bold text-white text-xs lg:text-sm whitespace-nowrap">
                            Rp {{ number_format($grandTotalBuku, 0, ',', '.') }}
                        </td>
                        <td class="px-3 lg:px-4 py-3 lg:py-4 text-center font-bold text-white text-xs lg:text-sm">
                            @php
                                $pctTotal = $grandTotalNilai > 0
                                    ? round(($grandTotalAkum / $grandTotalNilai) * 100, 1)
                                    : 0;
                            @endphp
                            {{ $pctTotal }}%
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 🔥 FILTER                                                    --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 sm:p-4 mb-5">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <select name="category" 
                    class="px-3 py-2.5 border-2 border-gray-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:border-brand-500 transition">
                <option value="">📂 Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <select name="unit" 
                    class="px-3 py-2.5 border-2 border-gray-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:border-brand-500 transition">
                <option value="">🏢 Semua Unit</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ request('unit') == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-brand-500 to-brand-600 text-white px-4 py-2.5 rounded-xl text-xs sm:text-sm font-medium inline-flex items-center justify-center gap-2">
                    <i class="fas fa-filter"></i>Filter
                </button>
                @if(request()->anyFilled(['category', 'unit']))
                    <a href="{{ route('super_admin.assets.depreciation') }}" 
                       class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-2.5 rounded-xl text-sm inline-flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ============================================================ --}}
    {{-- 🔥 RINCIAN PER UNIT                                          --}}
    {{-- ============================================================ --}}
    <div class="mb-4">
        <h2 class="text-xs sm:text-sm font-bold text-gray-600 uppercase tracking-wide mb-3 flex items-center gap-2">
            <i class="fas fa-list text-brand-500"></i>
            Rincian Per Unit
        </h2>
    </div>

    @forelse($unitSummary as $unitName => $summary)
    <div class="mb-5 sm:mb-6 bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">

        {{-- HEADER UNIT --}}
        <div class="bg-gradient-to-r from-brand-500 to-brand-600 px-3 sm:px-5 py-3 sm:py-4">
            <div class="flex items-center gap-2 sm:gap-3 mb-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white/20 flex items-center justify-center shrink-0 ring-2 ring-white/30">
                    <i class="fas fa-building text-white text-sm sm:text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="text-white font-bold text-sm sm:text-base lg:text-lg leading-tight truncate">{{ $unitName }}</h3>
                    <p class="text-white/80 text-[10px] sm:text-xs">
                        {{ $summary['jumlah_item'] }} jenis barang — {{ number_format($summary['total_barang']) }} unit total
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2 sm:gap-3">
                <div class="bg-white/15 rounded-lg px-2 sm:px-3 py-1.5 backdrop-blur-sm">
                    <p class="text-white/70 text-[9px] sm:text-[10px] uppercase">Nilai Aset</p>
                    <p class="text-white font-bold text-[10px] sm:text-xs break-all">
                        Rp {{ number_format($summary['total_nilai'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-white/15 rounded-lg px-2 sm:px-3 py-1.5 backdrop-blur-sm">
                    <p class="text-white/70 text-[9px] sm:text-[10px] uppercase">Penyusutan</p>
                    <p class="text-white font-bold text-[10px] sm:text-xs break-all">
                        Rp {{ number_format($summary['total_akumulasi'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-white/15 rounded-lg px-2 sm:px-3 py-1.5 backdrop-blur-sm">
                    <p class="text-white/70 text-[9px] sm:text-[10px] uppercase">Nilai Buku</p>
                    <p class="text-white font-bold text-[10px] sm:text-xs break-all">
                        Rp {{ number_format($summary['total_buku'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- 📱 MOBILE VIEW: Card per barang --}}
        <div class="block md:hidden divide-y divide-gray-100">
            @foreach($summary['items'] as $item)
            @php
                $pct = $item->getDepreciationPercentage();
                $isHabis = $item->isFullyDepreciated();
                $totalHarga = $item->price * $item->stock;
            @endphp
            <div class="p-3 {{ $isHabis ? 'bg-red-50/40' : '' }}">
                {{-- Header: Barang + % --}}
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-sm text-gray-800 truncate">{{ $item->name }}</p>
                        <div class="inline-flex items-start gap-1 px-1.5 py-0.5 rounded border mt-1 max-w-full"
                             style="background: #0F6B5F10; border-color: #0F6B5F30;">
                            <i class="fas fa-qrcode text-[8px] mt-0.5 shrink-0" style="color: #0F6B5F;"></i>
                            <span class="font-mono text-[8px] font-semibold break-all" style="color: #0F6B5F;">
                                {{ $item->full_code ?? $item->code }}
                            </span>
                        </div>
                    </div>
                    <div class="shrink-0 text-right">
                        <span class="inline-block px-2 py-1 text-[10px] font-bold rounded-full
                            {{ $isHabis ? 'bg-red-100 text-red-700' : ($pct >= 75 ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700') }}">
                            {{ $pct }}%
                        </span>
                        @if($isHabis)
                            <div class="text-[9px] text-red-500 mt-0.5 font-bold">HABIS</div>
                        @endif
                    </div>
                </div>

                {{-- Info grid: Kategori, Tgl, Umur --}}
                <div class="grid grid-cols-3 gap-2 mb-2 text-center">
                    <div>
                        <p class="text-[9px] text-gray-500 uppercase">Kategori</p>
                        <p class="text-[10px] font-medium text-gray-800 truncate">{{ $item->category->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] text-gray-500 uppercase">Tgl Beli</p>
                        <p class="text-[10px] font-medium text-gray-800">
                            {{ $item->purchase_date ? $item->purchase_date->format('d/m/Y') : '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[9px] text-gray-500 uppercase">Umur</p>
                        <p class="text-[10px] font-medium text-gray-800">
                            {{ round($item->getYearsInUse(), 1) }}/{{ $item->getUsefulLifeYears() }}th
                        </p>
                    </div>
                </div>

                {{-- 🔥 HARGA ASLI + STOK + TOTAL HARGA --}}
                <div class="grid grid-cols-3 gap-2 mb-2">
                    <div class="bg-gray-50 rounded-lg p-2 text-center">
                        <p class="text-[9px] text-gray-500 uppercase">Harga Asli</p>
                        <p class="text-[10px] font-bold text-gray-800 break-all">
                            Rp {{ number_format($item->price, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-brand-50 rounded-lg p-2 text-center">
                        <p class="text-[9px] text-brand-700 uppercase">Stok</p>
                        <p class="text-[10px] font-bold text-brand-700">
                            {{ $item->stock }} unit
                        </p>
                    </div>
                    <div class="bg-amber-50 rounded-lg p-2 text-center">
                        <p class="text-[9px] text-amber-700 uppercase">Total</p>
                        <p class="text-[10px] font-bold text-amber-600 break-all">
                            Rp {{ number_format($totalHarga, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- Nilai: 4 kolom (susut & nilai buku per unit) --}}
                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-blue-50 rounded-lg p-2">
                        <p class="text-[9px] text-blue-700 uppercase">Susut/Thn (Unit)</p>
                        <p class="text-[10px] font-bold text-blue-600 break-all">
                            Rp {{ number_format($item->getAnnualDepreciation(), 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-red-50 rounded-lg p-2">
                        <p class="text-[9px] text-red-700 uppercase">Akumulasi (Unit)</p>
                        <p class="text-[10px] font-bold text-red-600 break-all">
                            Rp {{ number_format($item->getAccumulatedDepreciation(), 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-2">
                        <p class="text-[9px] text-green-700 uppercase">Nilai Buku (Unit)</p>
                        <p class="text-[10px] font-bold text-green-600 break-all">
                            Rp {{ number_format($item->getBookValue(), 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-2">
                        <p class="text-[9px] text-purple-700 uppercase">Total Buku (×Stok)</p>
                        <p class="text-[10px] font-bold text-purple-600 break-all">
                            Rp {{ number_format($item->getBookValue() * $item->stock, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Footer Total Unit (mobile) --}}
            <div class="bg-gray-100 p-3">
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="bg-white rounded-lg p-2 border border-gray-200">
                        <p class="text-[9px] font-bold text-gray-700 uppercase">Nilai Aset</p>
                        <p class="text-[10px] font-bold text-blue-600 break-all mt-0.5">
                            Rp {{ number_format($summary['total_nilai'], 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-white rounded-lg p-2 border border-gray-200">
                        <p class="text-[9px] font-bold text-gray-700 uppercase">Penyusutan</p>
                        <p class="text-[10px] font-bold text-red-600 break-all mt-0.5">
                            Rp {{ number_format($summary['total_akumulasi'], 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-white rounded-lg p-2 border border-gray-200">
                        <p class="text-[9px] font-bold text-gray-700 uppercase">Nilai Buku</p>
                        <p class="text-[10px] font-bold text-green-600 break-all mt-0.5">
                            Rp {{ number_format($summary['total_buku'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 💻 TABLET & DESKTOP: Tabel --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 lg:px-4 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase">Barang</th>
                        <th class="px-3 lg:px-4 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase">Kategori</th>
                        <th class="px-3 lg:px-4 py-3 text-right text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase">Harga Asli</th>
                        <th class="px-3 lg:px-4 py-3 text-center text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase">Stok</th>
                        <th class="px-3 lg:px-4 py-3 text-right text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase bg-amber-50">Total Harga</th>
                        <th class="px-3 lg:px-4 py-3 text-center text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase">Umur</th>
                        <th class="px-3 lg:px-4 py-3 text-right text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase">Susut/Thn</th>
                        <th class="px-3 lg:px-4 py-3 text-right text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase">Akumulasi</th>
                        <th class="px-3 lg:px-4 py-3 text-right text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase">Nilai Buku</th>
                        <th class="px-3 lg:px-4 py-3 text-center text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($summary['items'] as $item)
                    @php
                        $pct = $item->getDepreciationPercentage();
                        $isHabis = $item->isFullyDepreciated();
                        $totalHarga = $item->price * $item->stock;
                    @endphp
                    <tr class="hover:bg-gray-50 transition {{ $isHabis ? 'bg-red-50/40' : '' }}">
                        <td class="px-3 lg:px-4 py-3">
                            <div class="text-xs lg:text-sm font-medium text-gray-800">{{ $item->name }}</div>
                            <div class="font-mono text-[9px] lg:text-[10px] font-semibold mt-0.5 break-all"
                                 style="color: #0F6B5F;">
                                {{ $item->full_code ?? $item->code }}
                            </div>
                        </td>

                        <td class="px-3 lg:px-4 py-3 text-xs lg:text-sm text-gray-600">
                            <span class="inline-block px-2 py-0.5 text-[10px] lg:text-xs font-medium rounded-full bg-blue-50 text-blue-700">
                                {{ $item->category->name ?? '-' }}
                            </span>
                        </td>

                        {{-- 🔥 Harga Asli --}}
                        <td class="px-3 lg:px-4 py-3 text-right text-xs lg:text-sm text-gray-700 font-medium whitespace-nowrap">
                            Rp {{ number_format($item->price, 0, ',', '.') }}
                        </td>

                        {{-- 🔥 Stok --}}
                        <td class="px-3 lg:px-4 py-3 text-center text-xs lg:text-sm text-gray-700">
                            <span class="inline-block px-2 py-0.5 text-[10px] lg:text-xs font-bold rounded-full bg-brand-50 text-brand-700">
                                {{ $item->stock }}
                            </span>
                        </td>

                        {{-- 🔥 Total Harga (×Stok) — HIGHLIGHT --}}
                        <td class="px-3 lg:px-4 py-3 text-right text-xs lg:text-sm text-amber-600 font-bold whitespace-nowrap bg-amber-50/50">
                            Rp {{ number_format($totalHarga, 0, ',', '.') }}
                        </td>

                        <td class="px-3 lg:px-4 py-3 text-center text-[10px] lg:text-xs text-gray-600 whitespace-nowrap">
                            <span class="font-bold text-gray-800">{{ round($item->getYearsInUse(), 1) }}</span>
                            <span class="text-gray-400">/ {{ $item->getUsefulLifeYears() }}</span>
                        </td>

                        <td class="px-3 lg:px-4 py-3 text-right text-xs lg:text-sm text-blue-600 font-medium whitespace-nowrap">
                            Rp {{ number_format($item->getAnnualDepreciation(), 0, ',', '.') }}
                        </td>

                        <td class="px-3 lg:px-4 py-3 text-right text-xs lg:text-sm text-red-600 font-medium whitespace-nowrap">
                            Rp {{ number_format($item->getAccumulatedDepreciation(), 0, ',', '.') }}
                        </td>

                        <td class="px-3 lg:px-4 py-3 text-right text-xs lg:text-sm text-green-600 font-bold whitespace-nowrap">
                            Rp {{ number_format($item->getBookValue(), 0, ',', '.') }}
                        </td>

                        <td class="px-3 lg:px-4 py-3 text-center">
                            <span class="inline-block px-2 py-1 text-[10px] lg:text-xs font-bold rounded-full
                                {{ $isHabis ? 'bg-red-100 text-red-700' : ($pct >= 75 ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700') }}">
                                {{ $pct }}%
                            </span>
                            @if($isHabis)
                                <div class="text-[9px] text-red-500 mt-0.5 font-medium">HABIS</div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>

                <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                    <tr>
                        <td colspan="4" class="px-3 lg:px-4 py-3 text-xs lg:text-sm font-bold text-gray-700 text-right">
                            TOTAL {{ $unitName }}:
                        </td>
                        {{-- Total Harga (×Stok) --}}
                        <td class="px-3 lg:px-4 py-3 text-right text-xs lg:text-sm font-bold text-amber-700 whitespace-nowrap bg-amber-50/50">
                            Rp {{ number_format($summary['total_nilai'], 0, ',', '.') }}
                        </td>
                        <td class="px-3 lg:px-4 py-3 text-center text-[10px] lg:text-xs text-gray-500">
                            {{ number_format($summary['total_barang']) }} unit
                        </td>
                        <td class="px-3 lg:px-4 py-3"></td>
                        <td class="px-3 lg:px-4 py-3 text-right text-xs lg:text-sm font-bold text-red-600 whitespace-nowrap">
                            Rp {{ number_format($summary['total_akumulasi'], 0, ',', '.') }}
                        </td>
                        <td class="px-3 lg:px-4 py-3 text-right text-xs lg:text-sm font-bold text-green-600 whitespace-nowrap">
                            Rp {{ number_format($summary['total_buku'], 0, ',', '.') }}
                        </td>
                        <td class="px-3 lg:px-4 py-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl shadow-sm p-8 sm:p-12 text-center border border-gray-100">
        <i class="fas fa-inbox text-4xl sm:text-5xl block mb-3 text-gray-300"></i>
        <p class="text-gray-600 font-medium mb-1 text-sm sm:text-base">Belum ada data penyusutan</p>
        <p class="text-xs sm:text-sm text-gray-400">Pastikan barang sudah punya <strong>harga</strong> & <strong>tanggal beli</strong></p>
    </div>
    @endforelse

</div>
@endsection