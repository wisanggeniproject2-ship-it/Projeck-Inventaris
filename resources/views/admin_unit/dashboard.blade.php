@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Dashboard Admin Unit</h1>
        <div class="text-sm text-gray-500">
            <i class="fas fa-building mr-1"></i>
            {{ auth()->user()->unit->name ?? 'Unit' }}
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Barang</p>
                    <p class="text-2xl font-bold">{{ $stats['total_items'] }}</p>
                </div>
                <i class="fas fa-boxes text-3xl text-blue-500"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Dipinjam</p>
                    <p class="text-2xl font-bold">{{ $stats['total_borrowed'] }}</p>
                </div>
                <i class="fas fa-hand-paper text-3xl text-yellow-500"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Pending</p>
                    <p class="text-2xl font-bold">{{ $stats['total_pending'] }}</p>
                </div>
                <i class="fas fa-clock text-3xl text-orange-500"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Dikembalikan</p>
                    <p class="text-2xl font-bold">{{ $stats['total_returned'] }}</p>
                </div>
                <i class="fas fa-undo-alt text-3xl text-green-500"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total User</p>
                    <p class="text-2xl font-bold">{{ $stats['total_users'] }}</p>
                </div>
                <i class="fas fa-users text-3xl text-purple-500"></i>
            </div>
        </div>
    </div>
    
   {{-- ============================================================ --}}
    {{-- 🔥🔥🔥 CARD: BARANG YANG BARU DIHANCURKAN (Khusus Unit Ini)  --}}
    {{-- ============================================================ --}}
    @php
        // 🔥 Ambil barang dihancurkan dari unit yang sedang login
        $recentDisposals = collect();
        if (class_exists(\App\Models\AssetDisposal::class)) {
            $recentDisposals = \App\Models\AssetDisposal::with(['item', 'user', 'itemStock'])
                ->whereHas('item', function($q) {
                    $q->where('unit_id', auth()->user()->unit_id);
                })
                ->where('status', 'approved')
                ->whereNotNull('stock_code')
                ->latest('approved_at')
                ->take(8)
                ->get();
        }
    @endphp

    @if($recentDisposals->count() > 0)
    <div class="bg-white rounded-lg shadow p-4 sm:p-5 mb-6 border-l-4 border-l-red-500">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2 text-sm sm:text-base">
                <span class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-trash-can text-red-600 text-sm"></i>
                </span>
                Barang yang Baru Dihancurkan
                <span class="ml-1 px-2 py-0.5 text-[10px] sm:text-xs font-bold rounded-full bg-red-500 text-white">
                    {{ $recentDisposals->count() }}
                </span>
            </h3>
            <span class="text-[10px] sm:text-xs text-gray-400 hidden sm:block">
                Unit {{ auth()->user()->unit->name ?? '-' }}
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
            @foreach($recentDisposals as $disposal)
            <div class="bg-white rounded-xl border border-red-100 overflow-hidden hover:shadow-md hover:border-red-300 transition group">

                {{-- Foto Barang + Badge DIHAPUS --}}
                <div class="relative h-32 bg-gray-100">
                    <img src="{{ $disposal->item->image_url ?? asset('assets/images/default-item.png') }}"
                         alt="{{ $disposal->item->name ?? '-' }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                    <div class="absolute inset-0 bg-gradient-to-t from-red-900/70 via-red-900/20 to-transparent"></div>

                    <div class="absolute top-2 right-2">
                        <span class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-bold rounded-full bg-red-500 text-white shadow-md ring-2 ring-white/50">
                            <i class="fas fa-trash-can text-[8px]"></i>
                            DIHAPUS
                        </span>
                    </div>

                    <div class="absolute bottom-2 left-2 text-[10px] text-white font-medium">
                        <i class="fas fa-calendar-check text-[9px] mr-1"></i>
                        {{ $disposal->approved_at ? $disposal->approved_at->format('d/m/Y') : $disposal->created_at->format('d/m/Y') }}
                    </div>
                </div>

                {{-- Info --}}
                <div class="p-3">
                    <p class="text-sm font-bold text-gray-800 truncate" title="{{ $disposal->item->name ?? '-' }}">
                        {{ $disposal->item->name ?? 'Barang tidak ditemukan' }}
                    </p>

                    {{-- Kode stok --}}
                    @if($disposal->stock_code)
                    <div class="inline-flex items-start gap-1 mt-1.5 px-1.5 py-0.5 rounded-md max-w-full"
                         style="background: #0F6B5F15; color: #0F6B5F;">
                        <i class="fas fa-barcode text-[8px] mt-0.5 shrink-0"></i>
                        <span class="font-mono text-[9px] font-semibold leading-tight break-all">
                            {{ $disposal->stock_code }}
                        </span>
                    </div>
                    @endif

                    <div class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between text-[10px] text-gray-500">
                        <span class="truncate">
                            <i class="fas fa-user mr-1"></i>
                            {{ $disposal->user->name ?? '-' }}
                        </span>
                        @if($disposal->approved_at)
                        <span class="shrink-0 ml-2">
                            <i class="fas fa-clock mr-1"></i>
                            {{ $disposal->approved_at->format('H:i') }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Items -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="font-semibold text-lg">Barang Terbaru</h3>
            <a href="{{ route('admin_unit.items.index') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                Lihat Semua →
            </a>
        </div>
        <div class="overflow-x-auto">
            @if($recentItems->count() > 0)
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentItems as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-mono text-[11px] font-semibold leading-tight break-all"
                                 style="color: #0F6B5F;">
                                {{ $item->full_code ?? $item->code }}
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ $item->name }}</td>
                        <td class="px-6 py-4">{{ $item->category->name }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : 
                                   ($item->status == 'borrowed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ $item->status == 'available' ? 'Tersedia' : ($item->status == 'borrowed' ? 'Dipinjam' : 'Perbaikan') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin_unit.items.show', $item) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-box-open text-4xl mb-2"></i>
                <p>Belum ada barang di unit ini</p>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Recent Circulations -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="font-semibold text-lg">Peminjaman Terbaru</h3>
            <a href="{{ route('admin_unit.circulations.index') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                Lihat Semua →
            </a>
        </div>
        <div class="overflow-x-auto">
            @if($recentCirculations->count() > 0)
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peminjam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Pinjam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentCirculations as $circulation)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">{{ $circulation->item->name }}</td>
                        <td class="px-6 py-4">{{ $circulation->borrower_name }}</td>
                        <td class="px-6 py-4">{{ $circulation->borrow_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $circulation->status == 'approved' ? 'bg-green-100 text-green-700' : 
                                   ($circulation->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                   ($circulation->status == 'returned' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                                {{ ucfirst($circulation->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-exchange-alt text-4xl mb-2"></i>
                <p>Belum ada peminjaman di unit ini</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection