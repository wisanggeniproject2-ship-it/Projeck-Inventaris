@extends('layouts.app')

@section('content')
@php
    $prefix = auth()->user()->role === 'super_admin' ? 'super_admin' : 'admin_unit';
@endphp

<div class="container mx-auto px-3 sm:px-4 lg:px-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-5 sm:mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
            <i class="fas fa-boxes-stacked text-brand-500 mr-2"></i>Manajemen Barang
        </h1>
        <a href="{{ route($prefix . '.items.create') }}" 
           class="bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-white px-4 py-2.5 rounded-xl transition-all text-sm font-medium shadow-sm hover:shadow-md inline-flex items-center justify-center gap-2 w-full sm:w-auto">
            <i class="fas fa-plus"></i>
            <span>Tambah Barang</span>
        </a>
    </div>

    {{-- FILTER --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-3 sm:p-4 mb-5">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            {{-- Search --}}
            <div class="relative">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari kode, nama, lokasi..." 
                       class="w-full pl-10 pr-3 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition">
            </div>

            {{-- Unit --}}
            <div>
                <select name="unit" 
                        class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition">
                    <option value="">🏢 Semua Unit</option>
                    @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ request('unit') == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Condition --}}
            <div>
                <select name="condition" 
                        class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition">
                    <option value="">🔍 Semua Kondisi</option>
                    <option value="baik" {{ request('condition') == 'baik' ? 'selected' : '' }}>✅ Baik</option>
                    <option value="rusak" {{ request('condition') == 'rusak' ? 'selected' : '' }}>⚠️ Rusak</option>
                    <option value="perbaikan" {{ request('condition') == 'perbaikan' ? 'selected' : '' }}>🔧 Perbaikan</option>
                </select>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex gap-2">
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium inline-flex items-center justify-center gap-2 transition shadow-sm">
                    <i class="fas fa-search"></i>
                    <span>Cari</span>
                </button>
                @if(request('search') || request('unit') || request('condition'))
                <a href="{{ route($prefix . '.items.index') }}" 
                   class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-2.5 rounded-xl transition inline-flex items-center justify-center"
                   title="Reset">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ============================================================ --}}
    {{-- 📱 MOBILE VIEW — Card layout                                --}}
    {{-- ============================================================ --}}
    <div class="block md:hidden space-y-3">
        @forelse($items as $item)
        @php
            // 🔥 Total aktif (exclude disposed)
            $totalAktif     = $item->stockCodes()->where('status', '!=', 'disposed')->count();
            $availableStock = $item->available_stock;
            $borrowedStock  = $item->borrowed_stock;
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
            {{-- Header Card: Gambar + Nama --}}
            <div class="flex gap-3 p-3 border-b border-gray-100">
                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" 
                     class="w-16 h-16 rounded-xl object-cover border border-gray-200 shrink-0">
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-sm text-gray-800 truncate">{{ $item->name }}</h3>
                    
                    {{-- Kode Lengkap --}}
                    <div class="inline-flex items-start gap-1 px-2 py-1 rounded-md border mt-1 max-w-full"
                         style="background: #0F6B5F10; border-color: #0F6B5F30;">
                        <i class="fas fa-qrcode text-[9px] mt-0.5 shrink-0" style="color: #0F6B5F;"></i>
                        <span class="font-mono text-[9px] font-semibold leading-tight break-all"
                              style="color: #0F6B5F;">
                            {{ $item->full_code ?? $item->code }}
                        </span>
                    </div>

                    {{-- Unit Badge --}}
                    <div class="mt-1.5">
                        <span class="inline-flex items-center gap-1 text-[10px] font-medium bg-teal-50 text-teal-700 px-2 py-0.5 rounded-md border border-teal-100">
                            <i class="fas fa-building text-[8px]"></i>
                            {{ $item->unit->name ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Body: Stok + Kondisi + Status --}}
            <div class="p-3 bg-gray-50 border-b border-gray-100">
                {{-- 🔥 STOK DETAIL — 3 KOLOM --}}
                <div class="mb-2">
                    <p class="text-[10px] text-gray-500 uppercase mb-1.5 text-center font-semibold">Stok Barang</p>
                    <div class="grid grid-cols-3 gap-1.5">
                        {{-- Total --}}
                        <div class="text-center p-1.5 bg-white rounded-lg border border-gray-200">
                            <p class="text-[9px] text-gray-500 uppercase font-semibold leading-none">Total</p>
                            <span class="inline-block text-sm font-bold text-gray-700 mt-0.5">{{ $totalAktif }}</span>
                        </div>

                        {{-- Tersedia --}}
                        <div class="text-center p-1.5 bg-green-50 rounded-lg border border-green-200">
                            <p class="text-[9px] text-green-700 uppercase font-semibold leading-none">Ready</p>
                            <span class="inline-block text-sm font-bold text-green-700 mt-0.5">{{ $availableStock }}</span>
                        </div>

                        {{-- Dipinjam --}}
                        <div class="text-center p-1.5 bg-orange-50 rounded-lg border border-orange-200">
                            <p class="text-[9px] text-orange-700 uppercase font-semibold leading-none">Pinjam</p>
                            <span class="inline-block text-sm font-bold text-orange-700 mt-0.5">{{ $borrowedStock }}</span>
                        </div>
                    </div>
                </div>

                {{-- Kondisi & Status --}}
                <div class="flex items-center justify-center gap-2 text-[10px]">
                    <span class="text-gray-500 uppercase">Kondisi:</span>
                    <span class="inline-block px-2 py-1 font-medium rounded-full
                        {{ $item->condition == 'baik' ? 'bg-green-100 text-green-700' : 
                           ($item->condition == 'rusak' ? 'bg-yellow-100 text-yellow-700' : 'bg-orange-100 text-orange-700') }}">
                        {{ ucfirst($item->condition) }}
                    </span>

                    <span class="text-gray-500 uppercase ml-2">Status:</span>
                    <span class="inline-block px-2 py-1 font-medium rounded-full
                        {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : 
                           ($item->status == 'borrowed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ $item->status == 'available' ? 'Tersedia' : ($item->status == 'borrowed' ? 'Dipinjam' : 'Perbaikan') }}
                    </span>
                </div>
            </div>

            {{-- Footer: Sumber Dana + QR --}}
            <div class="flex items-center justify-between gap-3 px-3 py-2.5 bg-white">
                <div class="flex items-center gap-2 min-w-0">
                    <i class="fas fa-hand-holding-dollar text-brand-500 text-xs"></i>
                    <span class="text-xs text-gray-600 truncate">
                        {{ $item->fundingSource->name ?? 'Tanpa sumber dana' }}
                    </span>
                </div>
                @if($item->qr_code_url)
                    <img src="{{ $item->qr_code_url }}" alt="QR" class="w-8 h-8 rounded border border-gray-200 shrink-0">
                @endif
            </div>

            {{-- Aksi --}}
            <div class="grid grid-cols-5 gap-1.5 p-2 border-t border-gray-100 bg-gray-50">
                <a href="{{ route($prefix . '.items.show', $item) }}" 
                   class="flex flex-col items-center justify-center gap-0.5 py-2 rounded-lg text-blue-600 bg-white hover:bg-blue-50 transition border border-gray-100"
                   title="Detail">
                    <i class="fas fa-eye text-sm"></i>
                    <span class="text-[9px] font-medium">Detail</span>
                </a>

                <a href="{{ route($prefix . '.items.pdf', $item) }}" 
                   target="_blank"
                   class="flex flex-col items-center justify-center gap-0.5 py-2 rounded-lg text-red-600 bg-white hover:bg-red-50 transition border border-gray-100"
                   title="PDF">
                    <i class="fas fa-file-pdf text-sm"></i>
                    <span class="text-[9px] font-medium">PDF</span>
                </a>

                <a href="{{ route('items.png', $item) }}" 
                   download
                   class="flex flex-col items-center justify-center gap-0.5 py-2 rounded-lg text-green-600 bg-white hover:bg-green-50 transition border border-gray-100"
                   title="PNG">
                    <i class="fas fa-file-image text-sm"></i>
                    <span class="text-[9px] font-medium">PNG</span>
                </a>

                <a href="{{ route($prefix . '.items.edit', $item) }}" 
                   class="flex flex-col items-center justify-center gap-0.5 py-2 rounded-lg text-yellow-600 bg-white hover:bg-yellow-50 transition border border-gray-100"
                   title="Edit">
                    <i class="fas fa-edit text-sm"></i>
                    <span class="text-[9px] font-medium">Edit</span>
                </a>

                <form action="{{ route($prefix . '.items.destroy', $item) }}" method="POST" class="w-full">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full flex flex-col items-center justify-center gap-0.5 py-2 rounded-lg text-red-600 bg-white hover:bg-red-50 transition border border-gray-100"
                            onclick="return confirm('Yakin hapus barang ini?')" 
                            title="Hapus">
                        <i class="fas fa-trash text-sm"></i>
                        <span class="text-[9px] font-medium">Hapus</span>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl shadow-sm p-8 text-center border border-gray-100">
            <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-gray-100 flex items-center justify-center">
                <i class="fas fa-box-open text-2xl text-gray-300"></i>
            </div>
            <p class="text-gray-600 font-medium mb-1">Belum ada data barang</p>
            <p class="text-xs text-gray-400">Klik "Tambah Barang" untuk mulai</p>
        </div>
        @endforelse
    </div>

    {{-- 💻 TABLET & DESKTOP VIEW --}}
    <div class="hidden md:block bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 lg:px-4 xl:px-6 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Gambar</th>
                        <th class="px-3 lg:px-4 xl:px-6 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-3 lg:px-4 xl:px-6 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-3 lg:px-4 xl:px-6 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-3 lg:px-4 xl:px-6 py-3 text-center text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-boxes text-gray-400 mr-1"></i>Stok
                        </th>
                        <th class="px-3 lg:px-4 xl:px-6 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Kondisi</th>
                        <th class="px-3 lg:px-4 xl:px-6 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-3 lg:px-4 xl:px-6 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Sumber Dana</th>
                        <th class="px-3 lg:px-4 xl:px-6 py-3 text-center text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider hidden xl:table-cell">QR</th>
                        <th class="px-3 lg:px-4 xl:px-6 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($items as $item)
                    @php
                        $totalAktif     = $item->stockCodes()->where('status', '!=', 'disposed')->count();
                        $availableStock = $item->available_stock;
                        $borrowedStock  = $item->borrowed_stock;
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        {{-- Gambar --}}
                        <td class="px-3 lg:px-4 xl:px-6 py-3 lg:py-4">
                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" 
                                 class="w-10 h-10 lg:w-12 lg:h-12 rounded-lg object-cover border border-gray-200">
                        </td>

                        {{-- Kode Lengkap --}}
                        <td class="px-3 lg:px-4 xl:px-6 py-3 lg:py-4">
                            <div class="inline-flex items-start gap-1 px-2 py-1 rounded-md border max-w-[180px] lg:max-w-none"
                                 style="background: #0F6B5F10; border-color: #0F6B5F30;">
                                <i class="fas fa-qrcode text-[9px] mt-0.5 shrink-0" style="color: #0F6B5F;"></i>
                                <span class="font-mono text-[9px] lg:text-[10px] font-semibold leading-tight break-all"
                                      style="color: #0F6B5F;">
                                    {{ $item->full_code ?? $item->code }}
                                </span>
                            </div>
                        </td>

                        {{-- Nama --}}
                        <td class="px-3 lg:px-4 xl:px-6 py-3 lg:py-4">
                            <div class="font-medium text-xs lg:text-sm text-gray-800 line-clamp-2 max-w-[150px] lg:max-w-none">
                                {{ $item->name }}
                            </div>
                        </td>

                        {{-- Unit --}}
                        <td class="px-3 lg:px-4 xl:px-6 py-3 lg:py-4">
                            <span class="inline-flex items-center gap-1 text-[10px] lg:text-xs font-medium bg-teal-50 text-teal-700 px-2 py-1 rounded-lg border border-teal-100">
                                <i class="fas fa-building text-[9px]"></i>
                                {{ $item->unit->name }}
                            </span>
                        </td>

                        {{-- 🔥 STOK — 3 badge saja --}}
                        <td class="px-3 lg:px-4 xl:px-6 py-3 lg:py-4">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Total --}}
                                <div class="text-center" title="Total unit aktif">
                                    <div class="inline-flex flex-col items-center px-2 py-0.5 rounded-md bg-gray-100 border border-gray-200 min-w-[42px]">
                                        <span class="text-[8px] text-gray-500 uppercase leading-none font-semibold">Total</span>
                                        <span class="text-xs lg:text-sm font-bold text-gray-700 leading-tight">{{ $totalAktif }}</span>
                                    </div>
                                </div>

                                {{-- Tersedia --}}
                                <div class="text-center" title="Stok tersedia (bisa dipinjam)">
                                    <div class="inline-flex flex-col items-center px-2 py-0.5 rounded-md bg-green-100 border border-green-200 min-w-[42px]">
                                        <span class="text-[8px] text-green-700 uppercase leading-none font-semibold">Ready</span>
                                        <span class="text-xs lg:text-sm font-bold text-green-700 leading-tight">{{ $availableStock }}</span>
                                    </div>
                                </div>

                                {{-- Dipinjam --}}
                                <div class="text-center" title="Stok sedang dipinjam">
                                    <div class="inline-flex flex-col items-center px-2 py-0.5 rounded-md bg-orange-100 border border-orange-200 min-w-[42px]">
                                        <span class="text-[8px] text-orange-700 uppercase leading-none font-semibold">Pinjam</span>
                                        <span class="text-xs lg:text-sm font-bold text-orange-700 leading-tight">{{ $borrowedStock }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Kondisi --}}
                        <td class="px-3 lg:px-4 xl:px-6 py-3 lg:py-4">
                            <span class="inline-block px-2 py-1 text-[10px] lg:text-xs font-medium rounded-full
                                {{ $item->condition == 'baik' ? 'bg-green-100 text-green-700' : 
                                   ($item->condition == 'rusak' ? 'bg-yellow-100 text-yellow-700' : 'bg-orange-100 text-orange-700') }}">
                                {{ ucfirst($item->condition) }}
                            </span>
                        </td>

                        {{-- Status --}}
                        <td class="px-3 lg:px-4 xl:px-6 py-3 lg:py-4">
                            <span class="inline-block px-2 py-1 text-[10px] lg:text-xs font-medium rounded-full
                                {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : 
                                   ($item->status == 'borrowed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ $item->status == 'available' ? 'Tersedia' : ($item->status == 'borrowed' ? 'Dipinjam' : 'Perbaikan') }}
                            </span>
                        </td>

                        {{-- Sumber Dana --}}
                        <td class="px-3 lg:px-4 xl:px-6 py-3 lg:py-4 hidden lg:table-cell text-xs lg:text-sm text-gray-600">
                            {{ $item->fundingSource->name ?? '-' }}
                        </td>

                        {{-- QR --}}
                        <td class="px-3 lg:px-4 xl:px-6 py-3 lg:py-4 hidden xl:table-cell text-center">
                            @if($item->qr_code_url)
                                <img src="{{ $item->qr_code_url }}" alt="QR" class="w-8 h-8 lg:w-10 lg:h-10 mx-auto rounded border border-gray-200">
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="px-3 lg:px-4 xl:px-6 py-3 lg:py-4">
                            <div class="flex gap-1.5 lg:gap-2 items-center flex-wrap">
                                <a href="{{ route($prefix . '.items.show', $item) }}" 
                                   class="text-blue-600 hover:text-blue-800 transition p-1" 
                                   title="Detail">
                                    <i class="fas fa-eye text-xs lg:text-sm"></i>
                                </a>
                                
                                <a href="{{ route($prefix . '.items.pdf', $item) }}" 
                                   target="_blank"
                                   class="text-red-600 hover:text-red-800 transition p-1" 
                                   title="PDF">
                                    <i class="fas fa-file-pdf text-xs lg:text-sm"></i>
                                </a>

                                <a href="{{ route('items.png', $item) }}" 
                                   download
                                   class="text-green-600 hover:text-green-800 transition p-1" 
                                   title="PNG">
                                    <i class="fas fa-file-image text-xs lg:text-sm"></i>
                                </a>
                                
                                <a href="{{ route($prefix . '.items.edit', $item) }}" 
                                   class="text-yellow-600 hover:text-yellow-800 transition p-1" 
                                   title="Edit">
                                    <i class="fas fa-edit text-xs lg:text-sm"></i>
                                </a>
                                
                                <form action="{{ route($prefix . '.items.destroy', $item) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-800 transition p-1" 
                                            onclick="return confirm('Yakin hapus?')" 
                                            title="Hapus">
                                        <i class="fas fa-trash text-xs lg:text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-12 text-center text-gray-500">
                            <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-box-open text-2xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-600 font-medium mb-1">Belum ada data barang</p>
                            <p class="text-xs text-gray-400">Klik "Tambah Barang" untuk mulai</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    @if($items->hasPages())
    <div class="mt-5 sm:mt-6">
        {{ $items->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection