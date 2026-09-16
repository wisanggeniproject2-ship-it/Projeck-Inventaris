@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                <i class="fas fa-boxes-stacked text-brand-500 mr-2"></i>Daftar Barang
            </h1>
            <p class="text-sm text-gray-500 mt-1">Semua unit — bisa dipinjam sesuai ketersediaan</p>
        </div>
        <div class="inline-flex items-center gap-2 bg-white border border-gray-100 shadow-sm rounded-xl px-4 py-2 text-sm text-gray-600 w-fit">
            <i class="fas fa-building text-brand-500"></i>
            <span>Unit Anda: <strong class="text-gray-800">{{ auth()->user()->unit->name }}</strong></span>
        </div>
    </div>

    {{-- INFO CARD --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-4 sm:p-5 mb-6 shadow-sm">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center shrink-0">
                <i class="fas fa-info-circle text-blue-500 text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-sm text-blue-800 font-semibold mb-1.5">Informasi Status Barang</p>
                <div class="flex flex-wrap gap-2 text-xs">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-100 text-green-700 rounded-lg font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                        Tersedia — bisa dipinjam
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-100 text-red-700 rounded-lg font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                        Dipinjam / Rusak / Perbaikan
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-lg font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                        Stok habis
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER TABS --}}
    <div class="bg-white rounded-2xl shadow-sm mb-4 overflow-hidden border border-gray-100">
        <div class="overflow-x-auto thin-scroll">
            <nav class="flex min-w-max">
                @php
                    $tabs = [
                        ['route' => ['status' => null, 'filter' => null],  'icon' => 'fa-list',         'label' => 'Semua',           'color' => 'blue'],
                        ['route' => ['status' => 'available'],             'icon' => 'fa-check-circle', 'label' => 'Tersedia',        'color' => 'green'],
                        ['route' => ['status' => 'unavailable'],           'icon' => 'fa-times-circle', 'label' => 'Tidak Tersedia',  'color' => 'red'],
                        ['route' => ['filter' => 'my'],                    'icon' => 'fa-user',         'label' => 'Pinjamanku',      'color' => 'purple'],
                    ];
                @endphp

                @foreach($tabs as $tab)
                    @php
                        $isActive = false;
                        if ($tab['route'] === ['status' => null, 'filter' => null]) {
                            $isActive = !request('status') && !request('filter');
                        } elseif (isset($tab['route']['status'])) {
                            $isActive = request('status') == $tab['route']['status'];
                        } elseif (isset($tab['route']['filter'])) {
                            $isActive = request('filter') == $tab['route']['filter'];
                        }
                        $color = $tab['color'];
                    @endphp
                    <a href="{{ route('user.items.index', array_filter($tab['route'])) }}"
                       class="inline-flex items-center gap-2 px-4 sm:px-5 py-3.5 border-b-2 text-xs sm:text-sm font-medium transition-all whitespace-nowrap
                       {{ $isActive 
                            ? "border-{$color}-500 text-{$color}-600 bg-{$color}-50/50" 
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                        <i class="fas {{ $tab['icon'] }}"></i>
                        {{ $tab['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-5 mb-6 border border-gray-100">
        <form method="GET" action="{{ route('user.items.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if(request('filter'))
                <input type="hidden" name="filter" value="{{ request('filter') }}">
            @endif

            <div class="md:col-span-1">
                <div class="relative">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari kode, nama, lokasi..."
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent text-sm transition">
                </div>
            </div>

            <div>
                <select name="unit" 
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent text-sm transition cursor-pointer">
                    <option value="">🏢 Semua Unit</option>
                    @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ request('unit') == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="category" 
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent text-sm transition cursor-pointer">
                    <option value="">📂 Semua Kategori</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                {{-- 🔥 TOMBOL CARI — WARNA HIJAU (seperti tombol Pinjam) --}}
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-4 py-2.5 rounded-xl transition-all text-sm font-medium shadow-sm hover:shadow-md hover:-translate-y-0.5 inline-flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i>
                    <span>Cari</span>
                </button>
                @if(request()->anyFilled(['search', 'unit', 'category', 'status', 'filter']))
                <a href="{{ route('user.items.index') }}" 
                   class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2.5 rounded-xl transition inline-flex items-center justify-center"
                   title="Reset filter">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- GRID CARD BARANG --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
        @forelse($items as $index => $item)
        @php
            $canBorrow = $item->canBeBorrowed();
            $isStockEmpty = $item->stock <= 0;
            $isBorrowed = $item->status == 'borrowed';
            $isBroken = $item->isBroken();
            $activeCirculation = $item->activeCirculation;
            
            // Tentukan status untuk badge + accent border
            if ($canBorrow) {
                $statusBadge    = 'Tersedia';
                $statusIcon     = 'fa-check-circle';
                $statusDotColor = 'bg-green-400';
                $statusBg       = 'bg-green-500';
                $accentBorder   = 'border-l-4 border-l-green-500';
                $cardBorder     = 'border-green-100 hover:border-green-300';
                $glowShadow     = 'hover:shadow-green-200/50';
                $ribbonColor    = 'from-green-500 to-emerald-500';
            } elseif ($isStockEmpty && $item->status == 'available') {
                $statusBadge    = 'Stok Habis';
                $statusIcon     = 'fa-box';
                $statusDotColor = 'bg-yellow-400';
                $statusBg       = 'bg-yellow-500';
                $accentBorder   = 'border-l-4 border-l-yellow-500';
                $cardBorder     = 'border-yellow-100 hover:border-yellow-300';
                $glowShadow     = 'hover:shadow-yellow-200/50';
                $ribbonColor    = 'from-yellow-500 to-amber-500';
            } elseif ($isBorrowed) {
                $statusBadge    = 'Dipinjam';
                $statusIcon     = 'fa-hand-paper';
                $statusDotColor = 'bg-red-400';
                $statusBg       = 'bg-red-500';
                $accentBorder   = 'border-l-4 border-l-red-500';
                $cardBorder     = 'border-red-100 hover:border-red-300';
                $glowShadow     = 'hover:shadow-red-200/50';
                $ribbonColor    = 'from-red-500 to-rose-500';
            } elseif ($isBroken) {
                $statusBadge    = $item->condition == 'rusak' ? 'Rusak' : 'Perbaikan';
                $statusIcon     = $item->condition == 'rusak' ? 'fa-triangle-exclamation' : 'fa-tools';
                $statusDotColor = 'bg-gray-400';
                $statusBg       = 'bg-gray-500';
                $accentBorder   = 'border-l-4 border-l-gray-500';
                $cardBorder     = 'border-gray-200 hover:border-gray-400';
                $glowShadow     = 'hover:shadow-gray-200/50';
                $ribbonColor    = 'from-gray-500 to-slate-500';
            } else {
                $statusBadge    = 'Tidak Tersedia';
                $statusIcon     = 'fa-times-circle';
                $statusDotColor = 'bg-red-400';
                $statusBg       = 'bg-red-500';
                $accentBorder   = 'border-l-4 border-l-red-500';
                $cardBorder     = 'border-red-100 hover:border-red-300';
                $glowShadow     = 'hover:shadow-red-200/50';
                $ribbonColor    = 'from-red-500 to-rose-500';
            }
        @endphp

        <div class="group bg-white rounded-3xl overflow-hidden border-2 {{ $accentBorder }} {{ $cardBorder }} transition-all duration-500 hover:-translate-y-1.5 hover:shadow-2xl {{ $glowShadow }}"
             style="animation: cardFadeIn 0.5s ease-out {{ $index * 0.05 }}s both;">

            {{-- GAMBAR --}}
            <div class="relative h-44 sm:h-48 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" 
                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">

                {{-- Gradient overlay bawah --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>

                {{-- 🔥 RIBBON Aksen warna di atas gambar --}}
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r {{ $ribbonColor }}"></div>

                {{-- BADGE STATUS --}}
                <div class="absolute top-4 right-3 z-10">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full {{ $statusBg }} text-white shadow-lg backdrop-blur-sm ring-2 ring-white/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                        {{ $statusBadge }}
                    </span>
                </div>

                {{-- LOCK OVERLAY kalau tidak tersedia --}}
                @if($isBorrowed || $isStockEmpty)
                    <div class="absolute inset-0 bg-black/30 backdrop-blur-[2px] flex items-center justify-center">
                        <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur flex items-center justify-center ring-4 ring-white/20">
                            <i class="fas fa-lock text-3xl text-white"></i>
                        </div>
                    </div>
                @endif

                {{-- EFEK GLOW di sudut gambar --}}
                <div class="absolute -bottom-8 -right-8 w-24 h-24 rounded-full bg-white/10 blur-2xl group-hover:bg-white/20 transition-all duration-500"></div>
            </div>

            {{-- KONTEN --}}
            <div class="p-4 sm:p-5 relative">
                {{-- NAMA --}}
                <h3 class="font-bold text-base sm:text-lg text-gray-800 mb-2 group-hover:text-brand-600 transition-colors line-clamp-1">
                    {{ $item->name }}
                </h3>

                {{-- TAGS --}}
                <div class="flex flex-wrap gap-1.5 mb-3">
                    <span class="inline-flex items-center gap-1 text-[11px] font-medium bg-blue-50 text-blue-700 px-2.5 py-1 rounded-lg border border-blue-100">
                        <i class="fas fa-tag text-[9px]"></i>{{ $item->category->name }}
                    </span>
                    <span class="inline-flex items-center gap-1 text-[11px] font-medium bg-teal-50 text-teal-700 px-2.5 py-1 rounded-lg border border-teal-100">
                        <i class="fas fa-building text-[9px]"></i>{{ $item->unit->name }}
                    </span>
                </div>

                {{-- INFO --}}
                <div class="space-y-1.5 mb-3 text-sm">
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="fas fa-barcode text-gray-400 w-4"></i>
                        <span class="font-mono text-xs">{{ $item->code }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="fas fa-boxes text-gray-400 w-4"></i>
                        <span>Stok: <strong class="{{ $item->stock > 0 ? 'text-green-600' : 'text-red-500' }}">{{ $item->stock }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="fas fa-map-marker-alt text-gray-400 w-4"></i>
                        <span class="line-clamp-1">{{ $item->location ?? '-' }}</span>
                    </div>
                </div>

                {{-- ALASAN TIDAK TERSEDIA --}}
                @if(!$canBorrow)
                    <div class="bg-gradient-to-r from-red-50 to-orange-50 border border-red-200 rounded-xl p-2.5 mb-3 text-xs space-y-0.5">
                        @if($isBorrowed && $activeCirculation)
                            <p class="text-red-700 flex items-center gap-1.5">
                                <i class="fas fa-user text-[10px]"></i>
                                Dipinjam: <strong>{{ $activeCirculation->borrower_name }}</strong>
                            </p>
                            <p class="text-red-600 flex items-center gap-1.5">
                                <i class="fas fa-calendar-alt text-[10px]"></i>
                                Kembali: {{ $activeCirculation->expected_return_date->format('d/m/Y') }}
                            </p>
                        @elseif($isStockEmpty && $item->status == 'available')
                            <p class="text-yellow-700 flex items-center gap-1.5 font-medium">
                                <i class="fas fa-exclamation-triangle text-[10px]"></i>
                                Stok habis, tunggu pengembalian
                            </p>
                        @elseif($item->condition == 'rusak')
                            <p class="text-yellow-700 flex items-center gap-1.5 font-medium">
                                <i class="fas fa-triangle-exclamation text-[10px]"></i>
                                Barang dalam kondisi rusak
                            </p>
                        @elseif($item->condition == 'perbaikan')
                            <p class="text-orange-700 flex items-center gap-1.5 font-medium">
                                <i class="fas fa-tools text-[10px]"></i>
                                Sedang dalam perbaikan
                            </p>
                        @endif
                    </div>
                @endif

                {{-- AKSI --}}
                <div class="flex gap-2">
                    <a href="{{ route('user.items.show', $item) }}" 
                       class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-center px-3 py-2.5 rounded-xl transition-all text-xs font-medium inline-flex items-center justify-center gap-1.5 hover:-translate-y-0.5 border border-gray-200">
                        <i class="fas fa-eye text-[10px]"></i>
                        Detail
                    </a>

                    @if($canBorrow)
                        {{-- 🔥 TOMBOL PINJAM — HIJAU (seperti tombol Cari) --}}
                        <a href="{{ route('user.circulations.create') }}?item={{ $item->id }}" 
                           class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white text-center px-3 py-2.5 rounded-xl transition-all text-xs font-medium inline-flex items-center justify-center gap-1.5 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                            <i class="fas fa-hand-paper text-[10px]"></i>
                            Pinjam
                        </a>
                    @else
                        <button disabled 
                                class="flex-1 bg-gray-100 text-gray-400 text-center px-3 py-2.5 rounded-xl cursor-not-allowed text-xs font-medium inline-flex items-center justify-center gap-1.5 border border-gray-200">
                            <i class="fas {{ $statusIcon }} text-[10px]"></i>
                            {{ $statusBadge }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="text-center py-16 bg-gradient-to-b from-gray-50 to-white rounded-3xl border-2 border-dashed border-gray-200">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-box-open text-4xl text-gray-300"></i>
                </div>
                <p class="text-gray-600 text-lg font-medium mb-1">Tidak ada barang ditemukan</p>
                <p class="text-gray-400 text-sm mb-4">Coba ubah filter atau kata kunci pencarian</p>
                <a href="{{ route('user.items.index') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-xl text-sm font-medium transition shadow-sm hover:shadow-md">
                    <i class="fas fa-rotate-left"></i>
                    Reset Filter
                </a>
            </div>
        </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    @if($items->hasPages())
    <div class="mt-8">
        {{ $items->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- ============================================================ --}}
{{-- STYLE: Animasi card                                       --}}
{{-- ============================================================ --}}
@push('styles')
<style>
    /* Animasi card muncul satu per satu */
    @keyframes cardFadeIn {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.98);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Line clamp utility */
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Thin scrollbar */
    .thin-scroll::-webkit-scrollbar {
        height: 4px;
    }
    .thin-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .thin-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .thin-scroll::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endpush
@endsection