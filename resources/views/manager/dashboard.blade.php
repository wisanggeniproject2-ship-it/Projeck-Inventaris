@extends('layouts.app')

@section('title', 'Dashboard Manager')

@section('content')
@php
    $totalItems    = $stats['total_items'] ?? 0;
    $totalBorrowed = $stats['total_borrowed'] ?? 0;
    $totalPending  = $stats['total_pending'] ?? 0;
    $totalUnits    = $stats['total_units'] ?? 0;
    $totalAvailable = max($totalItems - $totalBorrowed, 0);
@endphp

<div class="max-w-[1600px] mx-auto">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 animate-fadeInUp">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                Selamat datang kembali, <span class="text-brand-600">{{ Auth::user()->name }}!</span>
            </h1>
            <p class="text-sm text-gray-500 mt-1">Monitoring inventaris unit Anda</p>
        </div>
        <div class="flex items-center gap-2 bg-white border border-gray-100 shadow-sm rounded-xl px-4 py-2.5 text-sm text-gray-600 w-fit">
            <i class="fas fa-calendar-days text-brand-500"></i>
            {{ now()->translatedFormat('d F Y') }}
        </div>
    </div>

    {{-- STAT CARDS (Hijau Toska) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 stagger">
        <div class="card-elevated p-5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center mb-3 shadow-brand">
                <i class="fas fa-boxes-stacked text-white"></i>
            </div>
            <p class="text-sm text-gray-500">Total Barang</p>
            <p class="text-2xl font-bold text-gray-800 mt-0.5 tracking-tight">{{ number_format($totalItems) }}</p>
        </div>

        <div class="card-elevated p-5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center mb-3 shadow-[0_10px_30px_-10px_rgba(217,119,6,0.35)]">
                <i class="fas fa-hand-holding text-white"></i>
            </div>
            <p class="text-sm text-gray-500">Dipinjam</p>
            <p class="text-2xl font-bold text-gray-800 mt-0.5 tracking-tight">{{ number_format($totalBorrowed) }}</p>
        </div>

        <div class="card-elevated p-5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center mb-3 shadow-[0_10px_30px_-10px_rgba(220,38,38,0.35)]">
                <i class="fas fa-clock text-white"></i>
            </div>
            <p class="text-sm text-gray-500">Pending</p>
            <p class="text-2xl font-bold text-gray-800 mt-0.5 tracking-tight">{{ number_format($totalPending) }}</p>
        </div>

        <div class="card-elevated p-5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center mb-3 shadow-[0_10px_30px_-10px_rgba(5,150,105,0.35)]">
                <i class="fas fa-building text-white"></i>
            </div>
            <p class="text-sm text-gray-500">Total Unit</p>
            <p class="text-2xl font-bold text-gray-800 mt-0.5 tracking-tight">{{ number_format($totalUnits) }}</p>
        </div>
    </div>

    {{-- STATUS BARANG (Toska) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <div class="card-elevated p-5 animate-fadeInUp" style="animation-delay:.1s">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-brand-50 flex items-center justify-center">
                        <i class="fas fa-chart-pie text-brand-600 text-xs"></i>
                    </span>
                    Status Barang
                </h3>
            </div>

            @php
                $statusParts = [
                    ['label' => 'Tersedia', 'value' => $totalAvailable, 'color' => '#10b981', 'bar' => 'bg-emerald-500'],
                    ['label' => 'Dipinjam', 'value' => $totalBorrowed, 'color' => '#149c8c', 'bar' => 'bg-brand-500'],
                    ['label' => 'Pending', 'value' => $totalPending, 'color' => '#f87171', 'bar' => 'bg-red-400'],
                ];
            @endphp

            <ul class="space-y-3">
                @foreach($statusParts as $part)
                    @php $pct = $totalItems > 0 ? round(($part['value'] / $totalItems) * 100) : 0; @endphp
                    <li>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="flex items-center gap-2 text-gray-600">
                                <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $part['color'] }}"></span>
                                {{ $part['label'] }}
                            </span>
                            <span class="font-semibold text-gray-800">{{ number_format($part['value']) }} <span class="text-gray-400 font-normal">({{ $pct }}%)</span></span>
                        </div>
                        <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full rounded-full {{ $part['bar'] }} transition-all duration-700 ease-out" style="width: {{ $pct }}%"></div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- AKTIVITAS TERBARU (Toska) --}}
        <div class="card-elevated p-5 animate-fadeInUp" style="animation-delay:.18s">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-brand-50 flex items-center justify-center">
                        <i class="fas fa-bolt text-brand-600 text-[11px]"></i>
                    </span>
                    Aktivitas Terbaru
                </h3>
                <span class="text-xs text-gray-400">Monitoring</span>
            </div>
            <ul class="divide-y divide-gray-50 max-h-[260px] overflow-y-auto thin-scroll">
                @forelse($recentCirculations ?? [] as $circulation)
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
                            <p class="text-sm text-gray-700 truncate">
                                <span class="font-medium">{{ $circulation->item->name }}</span>
                                {{ $meta['label'] }} oleh
                                <span class="font-medium">{{ $circulation->borrower_name }}</span>
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
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

    {{-- BARANG PER UNIT (Toska) --}}
    <div class="card-elevated animate-fadeInUp mb-6" style="animation-delay:.26s">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-50 flex items-center justify-center">
                    <i class="fas fa-building text-brand-600 text-[11px]"></i>
                </span>
                Statistik Barang per Unit
            </h3>
            <span class="text-xs text-gray-400">Monitoring</span>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach($itemsByUnit as $unit)
                <div class="bg-gradient-to-br from-brand-50 to-white rounded-xl p-4 text-center border border-brand-100/50 hover:shadow-md transition">
                    <p class="text-sm text-gray-600 font-medium">{{ $unit->name }}</p>
                    <p class="text-2xl font-bold text-brand-600 mt-1">{{ $unit->items_count }}</p>
                    <p class="text-xs text-gray-400">Barang</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- BARANG TERBARU (Toska) --}}
    <div class="card-elevated animate-fadeInUp" style="animation-delay:.34s">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-50 flex items-center justify-center">
                    <i class="fas fa-boxes-stacked text-brand-600 text-[11px]"></i>
                </span>
                Barang Terbaru
            </h3>
            <a href="{{ route('manager.items.index') }}"
               class="text-xs font-medium bg-brand-50 text-brand-700 hover:bg-brand-100 px-3 py-1.5 rounded-lg transition">
                Lihat Semua
            </a>
        </div>
        <div class="overflow-x-auto">
            @if(isset($recentItems) && $recentItems->count() > 0)
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Kode</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Nama Barang</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Unit</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Lokasi</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($recentItems as $item)
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="px-5 py-3.5 font-medium text-gray-700">{{ $item->code }}</td>
                        <td class="px-5 py-3.5 text-gray-700">{{ $item->name }}</td>
                        <td class="px-5 py-3.5 text-gray-500">{{ $item->unit->name ?? '-' }}</td>
                        <td class="px-5 py-3.5 text-gray-500">{{ $item->location }}</td>
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full
                                {{ $item->status == 'available' ? 'bg-emerald-50 text-emerald-700' :
                                   ($item->status == 'borrowed' ? 'bg-brand-50 text-brand-700' : 'bg-amber-50 text-amber-700') }}">
                                {{ $item->status == 'available' ? 'Tersedia' : ($item->status == 'borrowed' ? 'Dipinjam' : 'Perbaikan') }}
                            </span>
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
</div>

@push('styles')
<style>
    .card-elevated {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.04);
        transition: all 0.2s ease;
    }
    .card-elevated:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
    }

    .shadow-brand {
        box-shadow: 0 10px 30px -10px rgba(13, 148, 136, 0.35);
    }

    .animate-fadeInUp {
        animation: fadeInUp 0.6s ease forwards;
        opacity: 0;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .stagger > *:nth-child(1) { animation-delay: 0.05s; }
    .stagger > *:nth-child(2) { animation-delay: 0.12s; }
    .stagger > *:nth-child(3) { animation-delay: 0.19s; }
    .stagger > *:nth-child(4) { animation-delay: 0.26s; }

    .thin-scroll::-webkit-scrollbar {
        width: 4px;
    }
    .thin-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .thin-scroll::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }
    .thin-scroll::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    .text-brand-50 { color: #f0fdfa; }
    .text-brand-100 { color: #ccfbf1; }
    .text-brand-400 { color: #2dd4bf; }
    .text-brand-500 { color: #14b8a6; }
    .text-brand-600 { color: #0d9488; }
    .text-brand-700 { color: #0f766e; }
    .text-brand-800 { color: #115e59; }

    .bg-brand-50 { background-color: #f0fdfa; }
    .bg-brand-100 { background-color: #ccfbf1; }
    .bg-brand-400 { background-color: #2dd4bf; }
    .bg-brand-500 { background-color: #14b8a6; }
    .bg-brand-600 { background-color: #0d9488; }

    .border-brand-100 { border-color: #ccfbf1; }
</style>
@endpush
@endsection