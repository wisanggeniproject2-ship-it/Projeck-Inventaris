@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-boxes-stacked text-brand-600 mr-2"></i>Daftar Barang
            </h1>
            <p class="text-sm text-gray-500 mt-1">Monitoring semua barang inventaris</p>
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-500 bg-brand-50 px-4 py-2 rounded-xl border border-brand-100">
            <i class="fas fa-eye text-brand-500"></i>
            <span>Mode Monitoring</span>
        </div>
    </div>

    {{-- STATISTIK CEPAT --}}
    @php
        $totalItems       = $items->total();
        $totalAvailable   = \App\Models\Item::where('status', 'available')->count();
        $totalBorrowed    = \App\Models\Item::where('status', 'borrowed')->count();
        $totalMaintenance = \App\Models\Item::where('status', 'maintenance')->count();
        $totalDisposed    = \App\Models\Item::where('status', 'disposed')->count();

        $totalNilaiAset = 0;
        foreach (\App\Models\Item::with('stockCodes')->get() as $it) {
            $h = (float) ($it->price ?? 0);
            $s = $it->stock_for_asset;
            $totalNilaiAset += $h * $s;
        }
    @endphp

    {{-- 5 STAT CARDS --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 mb-5">
        <div class="bg-white rounded-xl shadow-sm border-2 border-brand-100 p-4 text-center hover:shadow-md transition">
            <div class="w-10 h-10 mx-auto rounded-lg bg-brand-50 flex items-center justify-center mb-2">
                <i class="fas fa-boxes-stacked text-brand-600"></i>
            </div>
            <p class="text-2xl font-bold text-brand-600">{{ number_format($totalItems) }}</p>
            <p class="text-[10px] text-gray-500 uppercase font-semibold">Total Barang</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border-2 border-emerald-100 p-4 text-center hover:shadow-md transition">
            <div class="w-10 h-10 mx-auto rounded-lg bg-emerald-50 flex items-center justify-center mb-2">
                <i class="fas fa-check-circle text-emerald-600"></i>
            </div>
            <p class="text-2xl font-bold text-emerald-600">{{ number_format($totalAvailable) }}</p>
            <p class="text-[10px] text-gray-500 uppercase font-semibold">Tersedia</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border-2 border-amber-100 p-4 text-center hover:shadow-md transition">
            <div class="w-10 h-10 mx-auto rounded-lg bg-amber-50 flex items-center justify-center mb-2">
                <i class="fas fa-hand-paper text-amber-600"></i>
            </div>
            <p class="text-2xl font-bold text-amber-600">{{ number_format($totalBorrowed) }}</p>
            <p class="text-[10px] text-gray-500 uppercase font-semibold">Dipinjam</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border-2 border-orange-100 p-4 text-center hover:shadow-md transition">
            <div class="w-10 h-10 mx-auto rounded-lg bg-orange-50 flex items-center justify-center mb-2">
                <i class="fas fa-tools text-orange-600"></i>
            </div>
            <p class="text-2xl font-bold text-orange-600">{{ number_format($totalMaintenance) }}</p>
            <p class="text-[10px] text-gray-500 uppercase font-semibold">Perbaikan</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border-2 border-red-100 p-4 text-center hover:shadow-md transition col-span-2 sm:col-span-1">
            <div class="w-10 h-10 mx-auto rounded-lg bg-red-50 flex items-center justify-center mb-2">
                <i class="fas fa-trash-can text-red-600"></i>
            </div>
            <p class="text-2xl font-bold text-red-600">{{ number_format($totalDisposed) }}</p>
            <p class="text-[10px] text-gray-500 uppercase font-semibold">Dihapus</p>
        </div>
    </div>

    {{-- INFO CARD: NILAI ASET --}}
    <div class="bg-gradient-to-r from-amber-50 to-white rounded-xl shadow-sm border-2 border-amber-100 p-4 mb-5 flex items-center gap-3">
        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shrink-0 shadow-md">
            <i class="fas fa-coins text-white"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-[10px] text-amber-700 uppercase font-bold tracking-wide">Total Nilai Aset Inventaris</p>
            <p class="text-xl sm:text-2xl font-bold text-amber-600 leading-tight mt-0.5">
                Rp {{ number_format($totalNilaiAset, 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- FILTER & SEARCH --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="relative">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari kode, nama, atau lokasi..."
                       class="w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition text-sm">
            </div>

            <div>
                <select name="unit" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition text-sm bg-white">
                    <option value="">🏢 Semua Unit</option>
                    @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ request('unit') == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="status" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition text-sm bg-white">
                    <option value="">📋 Semua Status</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>✅ Tersedia</option>
                    <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>📤 Dipinjam</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>🔧 Perbaikan</option>
                    <option value="disposed" {{ request('status') == 'disposed' ? 'selected' : '' }}>🗑️ Dihapus</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition flex items-center justify-center gap-2 flex-1">
                    <i class="fas fa-search text-xs"></i>
                    Cari
                </button>
                @if(request('search') || request('unit') || request('status'))
                <a href="{{ route('manager.items.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-medium transition flex items-center justify-center" title="Reset">
                    <i class="fas fa-rotate-right"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- TABEL --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-brand-50">
                    <tr>
                        <th class="px-3 sm:px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">No</th>
                        <th class="px-3 sm:px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Kode Barang</th>
                        <th class="px-3 sm:px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Nama Barang</th>
                        <th class="px-3 sm:px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Unit</th>
                        <th class="px-3 sm:px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Stok Detail</th>
                        <th class="px-3 sm:px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Kode Stok Tersedia</th>
                        <th class="px-3 sm:px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Lokasi</th>
                        <th class="px-3 sm:px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Status</th>
                        <th class="px-3 sm:px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($items as $index => $item)
                    @php
                        $totalAktif       = $item->stockCodes()->where('status', '!=', 'disposed')->count();
                        $availableStock   = $item->available_stock;
                        $borrowedStock    = $item->borrowed_stock;
                        $maintenanceStock = $item->maintenance_stock;
                        $disposedStock    = $item->disposed_stock_count;

                        // 🔥 Ambil kode stok tersedia (untuk horizontal scroll)
                        $availableCodes = $item->availableStockCodes()->pluck('stock_code')->toArray();
                        $totalAvailableCodes = count($availableCodes);
                    @endphp
                    <tr class="hover:bg-brand-50/30 transition align-top">
                        {{-- No --}}
                        <td class="px-3 sm:px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                            {{ $items->firstItem() + $index }}
                        </td>

                        {{-- Kode Barang (Full Code) --}}
                        <td class="px-3 sm:px-4 py-4">
                            <div class="inline-flex items-start gap-1 px-2 py-1 rounded-md border max-w-[200px]"
                                 style="background: #0F6B5F10; border-color: #0F6B5F30;">
                                <i class="fas fa-qrcode text-[9px] mt-0.5 shrink-0" style="color: #0F6B5F;"></i>
                                <span class="font-mono text-[10px] font-semibold leading-tight break-all"
                                      style="color: #0F6B5F;">
                                    {{ $item->full_code ?? $item->code ?? '-' }}
                                </span>
                            </div>
                        </td>

                        {{-- Nama Barang --}}
                        <td class="px-3 sm:px-4 py-4">
                            <div class="flex items-center gap-3">
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                                         class="w-9 h-9 rounded-lg object-cover border border-gray-100 shrink-0">
                                @else
                                    <div class="w-9 h-9 rounded-lg bg-brand-50 flex items-center justify-center border border-gray-100 shrink-0">
                                        <i class="fas fa-box text-brand-400 text-sm"></i>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate" title="{{ $item->name }}">
                                        {{ $item->name }}
                                    </p>
                                    @if($item->category ?? false)
                                    <span class="inline-flex items-center gap-1 text-[9px] font-medium bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded border border-blue-100 mt-0.5">
                                        <i class="fas fa-tag text-[8px]"></i>{{ $item->category->name }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Unit --}}
                        <td class="px-3 sm:px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 text-[10px] font-medium bg-teal-50 text-teal-700 px-2 py-1 rounded-lg border border-teal-100">
                                <i class="fas fa-building text-[9px]"></i>
                                {{ $item->unit->name ?? '-' }}
                            </span>
                        </td>

                        {{-- STOK DETAIL --}}
                        <td class="px-3 sm:px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1">
                                {{-- Total --}}
                                <div class="inline-flex flex-col items-center px-2 py-0.5 rounded-md bg-gray-100 border border-gray-200 min-w-[42px]">
                                    <span class="text-[8px] text-gray-500 uppercase leading-none font-semibold">Total</span>
                                    <span class="text-xs font-bold text-gray-700 leading-tight">{{ $totalAktif }}</span>
                                </div>

                                {{-- Ready --}}
                                <div class="inline-flex flex-col items-center px-2 py-0.5 rounded-md bg-emerald-100 border border-emerald-200 min-w-[42px]">
                                    <span class="text-[8px] text-emerald-700 uppercase leading-none font-semibold">Ready</span>
                                    <span class="text-xs font-bold text-emerald-700 leading-tight">{{ $availableStock }}</span>
                                </div>

                                {{-- Pinjam --}}
                                <div class="inline-flex flex-col items-center px-2 py-0.5 rounded-md {{ $borrowedStock > 0 ? 'bg-amber-100 border border-amber-200' : 'bg-gray-50 border border-gray-200' }} min-w-[42px]">
                                    <span class="text-[8px] {{ $borrowedStock > 0 ? 'text-amber-700' : 'text-gray-400' }} uppercase leading-none font-semibold">Pinjam</span>
                                    <span class="text-xs font-bold {{ $borrowedStock > 0 ? 'text-amber-700' : 'text-gray-400' }} leading-tight">{{ $borrowedStock }}</span>
                                </div>

                                {{-- Dihapus --}}
                                @if($disposedStock > 0)
                                <div class="inline-flex flex-col items-center px-2 py-0.5 rounded-md bg-red-100 border border-red-200 min-w-[42px]">
                                    <span class="text-[8px] text-red-700 uppercase leading-none font-semibold">Hapus</span>
                                    <span class="text-xs font-bold text-red-700 leading-tight">{{ $disposedStock }}</span>
                                </div>
                                @endif
                            </div>
                        </td>

                        {{-- 🔥 KODE STOK TERSEDIA — HORIZONTAL SCROLL --}}
                        <td class="px-3 sm:px-4 py-4">
                            @if($totalAvailableCodes > 0)
                                <div class="flex items-center gap-2 max-w-[400px]">
                                    {{-- Horizontal scroll container --}}
                                    <div class="flex-1 flex gap-1.5 overflow-x-auto pb-1 thin-scroll-x">
                                        @foreach($availableCodes as $code)
                                        <div class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-[9px] font-mono font-semibold whitespace-nowrap shrink-0"
                                             style="background: #0F6B5F15; color: #0F6B5F;"
                                             title="{{ $code }}">
                                            <i class="fas fa-barcode text-[8px] shrink-0"></i>
                                            {{ $code }}
                                        </div>
                                        @endforeach
                                    </div>

                                    {{-- Badge total --}}
                                    <span class="shrink-0 inline-flex items-center gap-1 px-2 py-1 rounded-md text-[10px] font-bold bg-brand-100 text-brand-700 border border-brand-200"
                                          title="Total kode stok tersedia">
                                        <i class="fas fa-hashtag text-[9px]"></i>
                                        {{ $totalAvailableCodes }}
                                    </span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">Tidak ada kode tersedia</span>
                            @endif
                        </td>

                        {{-- Lokasi --}}
                        <td class="px-3 sm:px-4 py-4 text-sm text-gray-600 whitespace-nowrap">
                            <i class="fas fa-map-marker-alt text-gray-400 text-xs mr-1"></i>
                            {{ $item->location ?? '-' }}
                        </td>

                        {{-- Status --}}
                        <td class="px-3 sm:px-4 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center gap-1.5
                                {{ $item->status == 'available' ? 'bg-emerald-50 text-emerald-700' :
                                   ($item->status == 'borrowed' ? 'bg-brand-50 text-brand-700' :
                                   ($item->status == 'maintenance' ? 'bg-orange-50 text-orange-700' : 'bg-red-50 text-red-700')) }}">
                                <span class="w-1.5 h-1.5 rounded-full
                                    {{ $item->status == 'available' ? 'bg-emerald-500' :
                                       ($item->status == 'borrowed' ? 'bg-brand-500' :
                                       ($item->status == 'maintenance' ? 'bg-orange-500' : 'bg-red-500')) }}"></span>
                                {{ $item->status == 'available' ? 'Tersedia' :
                                   ($item->status == 'borrowed' ? 'Dipinjam' :
                                   ($item->status == 'maintenance' ? 'Perbaikan' : 'Dihapus')) }}
                            </span>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-3 sm:px-4 py-4 text-center whitespace-nowrap">
                            <a href="{{ route('manager.items.show', $item) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-600 hover:text-brand-700 transition text-xs font-medium"
                               title="Lihat Detail">
                                <i class="fas fa-eye text-sm"></i>
                                <span class="hidden sm:inline">Detail</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 sm:px-6 py-16 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 rounded-full bg-brand-50 flex items-center justify-center mb-4">
                                    <i class="fas fa-box-open text-3xl text-brand-300"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-600">Belum ada data barang</p>
                                <p class="text-xs text-gray-400 mt-1">Belum ada barang yang tersedia</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER --}}
        <div class="px-4 sm:px-6 py-3 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-gray-50/30">
            <div class="text-xs text-gray-500">
                Menampilkan <span class="font-medium text-gray-700">{{ $items->firstItem() ?? 0 }}</span>
                - <span class="font-medium text-gray-700">{{ $items->lastItem() ?? 0 }}</span>
                dari <span class="font-medium text-gray-700">{{ $items->total() }}</span> data
            </div>
            <div>
                {{ $items->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .text-brand-300 { color: #5eead4; }
    .text-brand-400 { color: #2dd4bf; }
    .text-brand-500 { color: #14b8a6; }
    .text-brand-600 { color: #0d9488; }
    .text-brand-700 { color: #0f766e; }

    .bg-brand-50 { background-color: #f0fdfa; }
    .bg-brand-100 { background-color: #ccfbf1; }
    .bg-brand-500 { background-color: #14b8a6; }
    .bg-brand-600 { background-color: #0d9488; }
    .bg-brand-700 { background-color: #0f766e; }

    .border-brand-100 { border-color: #ccfbf1; }
    .border-brand-500 { border-color: #14b8a6; }

    .focus\:border-brand-500:focus { border-color: #14b8a6; }
    .focus\:ring-brand-500\/30:focus { --tw-ring-color: rgba(13, 148, 136, 0.3); }

    .hover\:bg-brand-100:hover { background-color: #ccfbf1; }
    .hover\:bg-brand-700:hover { background-color: #0f766e; }
    .hover\:text-brand-700:hover { color: #0f766e; }

    .shadow-brand { box-shadow: 0 10px 30px -10px rgba(13, 148, 136, 0.35); }
    .transition { transition: all 0.2s ease; }

    /* ===== PAGINATION ===== */
    .pagination { display: flex; gap: 4px; flex-wrap: wrap; }
    .pagination .page-link {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 13px;
        color: #6b7280;
        background: transparent;
        transition: all 0.2s;
    }
    .pagination .page-link:hover { background: #f0fdfa; color: #0d9488; }
    .pagination .active .page-link { background: #0d9488; color: white; }
    .pagination .disabled .page-link { opacity: 0.5; cursor: not-allowed; }

    /* ===== SCROLLBAR (horizontal scroll kode stok) ===== */
    .thin-scroll-x::-webkit-scrollbar { height: 5px; }
    .thin-scroll-x::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    .thin-scroll-x::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .thin-scroll-x::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* ===== SCROLLBAR (tabel) ===== */
    .overflow-x-auto::-webkit-scrollbar { height: 6px; }
    .overflow-x-auto::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .overflow-x-auto::-webkit-scrollbar-thumb { background: #ccfbf1; border-radius: 10px; }
    .overflow-x-auto::-webkit-scrollbar-thumb:hover { background: #2dd4bf; }
</style>
@endpush
@endsection