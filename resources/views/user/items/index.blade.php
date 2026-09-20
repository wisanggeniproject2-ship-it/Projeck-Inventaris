@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6 animate-fadeInUp">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                <i class="fas fa-boxes-stacked mr-2" style="color: #0F6B5F;"></i>Daftar Barang
            </h1>
            <p class="text-sm text-gray-500 mt-1">Semua unit — bisa dipinjam sesuai ketersediaan</p>
        </div>
        <div class="inline-flex items-center gap-2 bg-white border border-gray-100 shadow-sm rounded-xl px-4 py-2 text-sm text-gray-600 w-fit">
            <i class="fas fa-building" style="color: #0F6B5F;"></i>
            <span>Unit Anda: <strong class="text-gray-800">{{ auth()->user()->unit->name }}</strong></span>
        </div>
    </div>

    {{-- INFO CARD --}}
    <div class="relative overflow-hidden rounded-2xl p-4 sm:p-5 mb-6 shadow-lg"
         style="background: linear-gradient(135deg, #0F6B5F 0%, #14857A 50%, #0F6B5F 100%);">
        {{-- Dekorasi background --}}
        <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute -bottom-10 -left-10 w-32 h-32 rounded-full bg-white/10 blur-2xl"></div>

        <div class="relative flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center shrink-0 ring-2 ring-white/30">
                <i class="fas fa-info-circle text-white text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-sm text-white font-semibold mb-2">Informasi Status Barang</p>
                <div class="flex flex-wrap gap-2 text-xs">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/95 text-emerald-800 rounded-lg font-medium shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Tersedia — bisa dipinjam
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/95 text-red-700 rounded-lg font-medium shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                        Dipinjam / Rusak / Perbaikan
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/95 text-yellow-700 rounded-lg font-medium shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                        Stok habis
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER TABS --}}
    <div class="bg-white rounded-2xl shadow-md mb-4 overflow-hidden border-2" style="border-color: #0F6B5F20;">
        <div class="overflow-x-auto thin-scroll">
            <nav class="flex min-w-max">
                @php
                    $tabs = [
                        ['route' => ['status' => null, 'filter' => null],  'icon' => 'fa-list',         'label' => 'Semua',           'color' => 'slate'],
                        ['route' => ['status' => 'available'],             'icon' => 'fa-check-circle', 'label' => 'Tersedia',        'color' => 'brand'],
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
                    @endphp
                    <a href="{{ route('user.items.index', array_filter($tab['route'])) }}"
                       class="group relative inline-flex items-center gap-2 px-4 sm:px-5 py-3.5 border-b-3 text-xs sm:text-sm font-medium transition-all whitespace-nowrap"
                       style="{{ $isActive 
                            ? 'border-color: #0F6B5F; color: #0F6B5F; background: linear-gradient(180deg, #0F6B5F08 0%, #0F6B5F15 100%);' 
                            : 'border-color: transparent; color: #6b7280;' }}"
                       onmouseover="if(!{{ $isActive ? 'true' : 'false' }}) { this.style.color='#0F6B5F'; this.style.background='#0F6B5F08'; }"
                       onmouseout="if(!{{ $isActive ? 'true' : 'false' }}) { this.style.color='#6b7280'; this.style.background='transparent'; }">
                        <i class="fas {{ $tab['icon'] }}"></i>
                        {{ $tab['label'] }}
                        @if($isActive)
                            <span class="absolute bottom-0 left-0 right-0 h-1 rounded-t-full" 
                                  style="background: linear-gradient(90deg, #0F6B5F, #14857A, #0F6B5F);"></span>
                        @endif
                    </a>
                @endforeach
            </nav>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="bg-white rounded-2xl shadow-md p-4 sm:p-5 mb-6 border-2" style="border-color: #0F6B5F20;">
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
                           class="w-full pl-10 pr-4 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-transparent focus:ring-2 text-sm transition"
                           style="--tw-ring-color: #0F6B5F;">
                </div>
            </div>

            <div>
                <select name="unit" 
                        class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-transparent focus:ring-2 text-sm transition cursor-pointer"
                        style="--tw-ring-color: #0F6B5F;">
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
                        class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-transparent focus:ring-2 text-sm transition cursor-pointer"
                        style="--tw-ring-color: #0F6B5F;">
                    <option value="">📂 Semua Kategori</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" 
                        class="group flex-1 text-white px-4 py-2.5 rounded-xl transition-all text-sm font-medium shadow-md hover:shadow-xl hover:-translate-y-0.5 inline-flex items-center justify-center gap-2 relative overflow-hidden"
                        style="background: linear-gradient(135deg, #0F6B5F 0%, #14857A 100%);">
                    <span class="absolute inset-0 bg-white/0 group-hover:bg-white/10 transition-all"></span>
                    <i class="fas fa-search relative"></i>
                    <span class="relative">Cari</span>
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
                $statusColor    = '#0F6B5F';   // 🔥 Brand color
                $statusColor2   = '#14857A';   // 🔥 Brand color lighter
                $accentColor    = '#0F6B5F';
                $ribbonColor    = 'linear-gradient(90deg, #0F6B5F 0%, #14857A 50%, #0F6B5F 100%)';
                $glowColor      = 'rgba(15, 107, 95, 0.4)';
            } elseif ($isStockEmpty && $item->status == 'available') {
                $statusBadge    = 'Stok Habis';
                $statusIcon     = 'fa-box';
                $statusColor    = '#f59e0b';
                $statusColor2   = '#fbbf24';
                $accentColor    = '#f59e0b';
                $ribbonColor    = 'linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%)';
                $glowColor      = 'rgba(245, 158, 11, 0.4)';
            } elseif ($isBorrowed) {
                $statusBadge    = 'Dipinjam';
                $statusIcon     = 'fa-hand-paper';
                $statusColor    = '#ef4444';
                $statusColor2   = '#f87171';
                $accentColor    = '#ef4444';
                $ribbonColor    = 'linear-gradient(90deg, #ef4444 0%, #f87171 100%)';
                $glowColor      = 'rgba(239, 68, 68, 0.4)';
            } elseif ($isBroken) {
                $statusBadge    = $item->condition == 'rusak' ? 'Rusak' : 'Perbaikan';
                $statusIcon     = $item->condition == 'rusak' ? 'fa-triangle-exclamation' : 'fa-tools';
                $statusColor    = '#6b7280';
                $statusColor2   = '#9ca3af';
                $accentColor    = '#6b7280';
                $ribbonColor    = 'linear-gradient(90deg, #6b7280 0%, #9ca3af 100%)';
                $glowColor      = 'rgba(107, 114, 128, 0.4)';
            } else {
                $statusBadge    = 'Tidak Tersedia';
                $statusIcon     = 'fa-times-circle';
                $statusColor    = '#ef4444';
                $statusColor2   = '#f87171';
                $accentColor    = '#ef4444';
                $ribbonColor    = 'linear-gradient(90deg, #ef4444 0%, #f87171 100%)';
                $glowColor      = 'rgba(239, 68, 68, 0.4)';
            }

            $hasPendingDisposal = $item->hasPendingDisposalRequest();
        @endphp

        {{-- 🔥 CARD dengan animasi baru --}}
        <div class="group relative bg-white rounded-3xl overflow-hidden transition-all duration-500 hover:-translate-y-2 card-glow"
             style="border: 3px solid {{ $accentColor }}30; animation: cardFadeIn 0.5s ease-out {{ $index * 0.05 }}s both; --glow-color: {{ $glowColor }};"
             onmouseover="this.style.borderColor='{{ $accentColor }}'; this.style.boxShadow='0 25px 50px -12px {{ $glowColor }}';"
             onmouseout="this.style.borderColor='{{ $accentColor }}30'; this.style.boxShadow='';">

            {{-- Shine effect --}}
            <div class="card-shine"></div>

            {{-- GAMBAR --}}
            <div class="relative h-44 sm:h-48 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" 
                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">

                <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                <div class="absolute top-0 left-0 right-0 h-1.5" style="background: {{ $ribbonColor }};"></div>

                <div class="absolute top-4 right-3 z-10">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-full text-white shadow-lg ring-2 ring-white/40 backdrop-blur-sm"
                          style="background: linear-gradient(135deg, {{ $statusColor }} 0%, {{ $statusColor2 }} 100%);">
                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                        {{ $statusBadge }}
                    </span>
                </div>

                @if($hasPendingDisposal)
                <div class="absolute top-4 left-3 z-10">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-full text-white shadow-lg ring-2 ring-white/40 backdrop-blur-sm bg-gradient-to-r from-orange-500 to-red-500 animate-pulse">
                        <i class="fas fa-trash-can text-[10px]"></i>
                        Diajukan Hapus
                    </span>
                </div>
                @endif

                @if($isBorrowed || $isStockEmpty)
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px] flex items-center justify-center">
                        <div class="w-16 h-16 rounded-full bg-white/25 backdrop-blur flex items-center justify-center ring-4 ring-white/30 float-anim">
                            <i class="fas fa-lock text-3xl text-white"></i>
                        </div>
                    </div>
                @endif

                {{-- Dekorasi lingkaran --}}
                <div class="absolute -bottom-8 -right-8 w-24 h-24 rounded-full bg-white/10 blur-2xl group-hover:bg-white/30 transition-all duration-500"></div>
            </div>

            {{-- KONTEN --}}
            <div class="p-4 sm:p-5 relative">
                <h3 class="font-bold text-base sm:text-lg text-gray-800 mb-2 transition-colors line-clamp-1 group-hover:text-gray-900">
                    {{ $item->name }}
                </h3>

                <div class="flex flex-wrap gap-1.5 mb-3">
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1 rounded-lg border"
                          style="background: #0F6B5F10; color: #0F6B5F; border-color: #0F6B5F30;">
                        <i class="fas fa-tag text-[9px]"></i>{{ $item->category->name }}
                    </span>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1 rounded-lg border"
                          style="background: #0F6B5F10; color: #0F6B5F; border-color: #0F6B5F30;">
                        <i class="fas fa-building text-[9px]"></i>{{ $item->unit->name }}
                    </span>
                </div>

                <div class="space-y-1.5 mb-3 text-sm">
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="fas fa-barcode text-gray-400 w-4"></i>
                        <span class="font-mono text-xs">{{ $item->code }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="fas fa-boxes text-gray-400 w-4"></i>
                        <span>Stok: 
                            <strong style="color: {{ $item->stock > 0 ? '#0F6B5F' : '#ef4444' }};">
                                {{ $item->stock }}
                            </strong>
                        </span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="fas fa-map-marker-alt text-gray-400 w-4"></i>
                        <span class="line-clamp-1">{{ $item->location ?? '-' }}</span>
                    </div>
                </div>

                @if(!$canBorrow)
                    <div class="rounded-xl p-2.5 mb-3 text-xs space-y-0.5 border-2"
                         style="background: linear-gradient(135deg, #fef2f2 0%, #fff7ed 100%); border-color: #fecaca;">
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
                       class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-center px-3 py-2.5 rounded-xl transition-all text-xs font-semibold inline-flex items-center justify-center gap-1.5 hover:-translate-y-0.5 border-2 border-gray-200">
                        <i class="fas fa-eye text-[10px]"></i>
                        Detail
                    </a>

                    @if($canBorrow)
                        {{-- 🔥 TOMBOL PINJAM --}}
                        <button type="button"
                                onclick="openUnitModal({{ $item->id }}, '{{ addslashes($item->name) }}')"
                                class="group/btn relative flex-1 text-white text-center px-3 py-2.5 rounded-xl transition-all text-xs font-bold inline-flex items-center justify-center gap-1.5 shadow-lg hover:shadow-2xl hover:-translate-y-0.5 overflow-hidden"
                                style="background: linear-gradient(135deg, #0F6B5F 0%, #14857A 100%);">
                            <span class="absolute inset-0 bg-white/0 group-hover/btn:bg-white/15 transition-all"></span>
                            <i class="fas fa-hand-paper text-[10px] relative group-hover/btn:rotate-12 transition-transform"></i>
                            <span class="relative">Pinjam</span>
                        </button>
                    @else
                        <button disabled 
                                class="flex-1 bg-gray-100 text-gray-400 text-center px-3 py-2.5 rounded-xl cursor-not-allowed text-xs font-semibold inline-flex items-center justify-center gap-1.5 border-2 border-gray-200">
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
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center float-anim">
                    <i class="fas fa-box-open text-4xl text-gray-300"></i>
                </div>
                <p class="text-gray-600 text-lg font-medium mb-1">Tidak ada barang ditemukan</p>
                <p class="text-gray-400 text-sm mb-4">Coba ubah filter atau kata kunci pencarian</p>
                <a href="{{ route('user.items.index') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-white rounded-xl text-sm font-medium transition shadow-lg hover:shadow-xl hover:-translate-y-0.5"
                   style="background: linear-gradient(135deg, #0F6B5F 0%, #14857A 100%);">
                    <i class="fas fa-rotate-left"></i>
                    Reset Filter
                </a>
            </div>
        </div>
        @endforelse
    </div>

    @if($items->hasPages())
    <div class="mt-8">
        {{ $items->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- ============================================================ --}}
{{-- MODAL PILIH UNIT                                            --}}
{{-- ============================================================ --}}
<div id="unitModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeUnitModal()"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden max-h-[90vh] flex flex-col" style="animation: modalIn 0.25s ease-out;">
        {{-- HEADER --}}
        <div class="px-5 py-4 flex items-center gap-3 shrink-0"
             style="background: linear-gradient(135deg, #0F6B5F 0%, #14857A 100%);">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shrink-0 ring-2 ring-white/30">
                <i class="fas fa-building text-white text-lg"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="text-white font-bold text-base">Pilih Unit</h3>
                <p class="text-white/80 text-xs">
                    Barang: <span class="font-semibold" id="modalItemName">-</span>
                </p>
            </div>
            <button type="button" onclick="closeUnitModal()" 
                    class="text-white/70 hover:text-white transition w-8 h-8 rounded-lg hover:bg-white/10 flex items-center justify-center">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- BODY --}}
        <div class="p-5 overflow-y-auto">
            <div class="rounded-xl p-3 mb-4 text-xs leading-relaxed border-2"
                 style="background: #0F6B5F10; border-color: #0F6B5F30; color: #0F6B5F;">
                <i class="fas fa-info-circle mr-1"></i>
                Pilih unit yang memiliki stok barang ini. Unit dengan stok habis tidak bisa dipilih.
            </div>

            {{-- Loading --}}
            <div id="unitLoading" class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-3xl" style="color: #0F6B5F;"></i>
                <p class="text-sm text-gray-500 mt-2">Memuat daftar unit...</p>
            </div>

            {{-- List Unit --}}
            <div id="unitList" class="space-y-2.5 hidden"></div>

            {{-- Kosong --}}
            <div id="unitEmpty" class="hidden text-center py-8">
                <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-box-open text-2xl text-gray-300"></i>
                </div>
                <p class="text-gray-500 font-medium">Tidak ada unit yang tersedia</p>
                <p class="text-xs text-gray-400 mt-1">Barang ini sedang tidak bisa dipinjam di unit manapun</p>
            </div>
        </div>
    </div>
</div>

<style>
    /* ============================================================ */
    /* 🔥 ANIMASI CARD                                              */
    /* ============================================================ */
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }

    @keyframes cardFadeIn {
        from { opacity: 0; transform: translateY(20px) scale(0.98); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    @keyframes unitFadeIn {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* 🔥 Float animation untuk icon */
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50%      { transform: translateY(-8px); }
    }

    .float-anim {
        animation: float 3s ease-in-out infinite;
    }

    /* 🔥 Shine effect (kilau melintas) */
    .card-shine {
        position: absolute;
        top: 0;
        left: -100%;
        width: 50%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
        transform: skewX(-25deg);
        pointer-events: none;
        z-index: 5;
    }

    .group:hover .card-shine {
        animation: shine 0.8s ease-out;
    }

    @keyframes shine {
        0%   { left: -100%; }
        100% { left: 150%; }
    }

    /* 🔥 Card glow on hover */
    .card-glow {
        position: relative;
    }

    .card-glow::before {
        content: '';
        position: absolute;
        inset: -3px;
        border-radius: 1.5rem;
        background: var(--glow-color);
        opacity: 0;
        filter: blur(20px);
        transition: opacity 0.5s ease;
        z-index: -1;
    }

    .card-glow:hover::before {
        opacity: 0.6;
    }

    .unit-card-anim {
        animation: unitFadeIn 0.3s ease-out both;
    }

    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

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

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .animate-fadeInUp {
        animation: fadeInUp 0.5s ease-out;
    }
</style>

@push('scripts')
<script>
    function openUnitModal(itemId, itemName) {
        const modal   = document.getElementById('unitModal');
        const loading = document.getElementById('unitLoading');
        const list    = document.getElementById('unitList');
        const empty   = document.getElementById('unitEmpty');

        document.getElementById('modalItemName').textContent = itemName;

        loading.classList.remove('hidden');
        list.classList.add('hidden');
        list.innerHTML = '';
        empty.classList.add('hidden');

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        fetch(`{{ url('user/items') }}/${itemId}/units`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            loading.classList.add('hidden');

            if (!data.units || data.units.length === 0) {
                empty.classList.remove('hidden');
                return;
            }

            list.classList.remove('hidden');

            data.units.forEach((unit, i) => {
                const canBorrow = unit.can_borrow;
                const itemId    = unit.item_id;

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = `unit-card-anim w-full text-left p-3 rounded-xl border-2 transition-all flex items-center gap-3 ${
                    canBorrow 
                        ? 'hover:shadow-lg hover:-translate-y-0.5 cursor-pointer' 
                        : 'bg-gray-50 cursor-not-allowed opacity-60'
                }`;
                btn.style.animationDelay = (i * 0.05) + 's';

                if (canBorrow) {
                    btn.style.borderColor = '#0F6B5F50';
                    btn.onmouseover = () => {
                        btn.style.borderColor = '#0F6B5F';
                        btn.style.background = '#0F6B5F08';
                    };
                    btn.onmouseout = () => {
                        btn.style.borderColor = '#0F6B5F50';
                        btn.style.background = '';
                    };
                    btn.onclick = function() {
                        window.location.href = `{{ url('user/circulations/create') }}?item=${itemId}`;
                    };
                } else {
                    btn.style.borderColor = '#e5e7eb';
                }

                btn.innerHTML = `
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 shadow-md"
                         style="background: ${canBorrow ? 'linear-gradient(135deg, #0F6B5F 0%, #14857A 100%)' : '#d1d5db'};">
                        <i class="fas fa-building text-white text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-800 text-sm truncate">${unit.name}</p>
                        <p class="text-xs mt-0.5" style="color: ${canBorrow ? '#0F6B5F' : '#6b7280'};">
                            ${canBorrow 
                                ? `<i class="fas fa-check-circle mr-1"></i>Stok tersedia: ${unit.stock}` 
                                : `<i class="fas fa-times-circle mr-1"></i>${unit.reason || 'Tidak tersedia'}`
                            }
                        </p>
                    </div>
                    <div class="shrink-0">
                        ${canBorrow 
                            ? `<div class="w-8 h-8 rounded-full flex items-center justify-center" style="background: #0F6B5F20;">
                                   <i class="fas fa-arrow-right text-xs" style="color: #0F6B5F;"></i>
                               </div>`
                            : `<div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                   <i class="fas fa-lock text-gray-400 text-xs"></i>
                               </div>`
                        }
                    </div>
                `;

                list.appendChild(btn);
            });
        })
        .catch(err => {
            console.error('Error:', err);
            loading.classList.add('hidden');
            empty.classList.remove('hidden');
        });
    }

    function closeUnitModal() {
        const modal = document.getElementById('unitModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeUnitModal();
    });
</script>
@endpush
@endsection