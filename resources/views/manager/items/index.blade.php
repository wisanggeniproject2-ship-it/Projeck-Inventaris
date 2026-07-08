
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <span class="text-brand-600">📦</span> Daftar Barang
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
        $totalItems = $items->total();
        $totalAvailable = $items->where('status', 'available')->count();
        $totalBorrowed = $items->where('status', 'borrowed')->count();
        $totalMaintenance = $items->where('status', 'maintenance')->count();
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center hover:shadow-md transition">
            <p class="text-2xl font-bold text-brand-600">{{ number_format($totalItems) }}</p>
            <p class="text-xs text-gray-500">Total Barang</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center hover:shadow-md transition">
            <p class="text-2xl font-bold text-emerald-600">{{ number_format($totalAvailable) }}</p>
            <p class="text-xs text-gray-500">Tersedia</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center hover:shadow-md transition">
            <p class="text-2xl font-bold text-amber-600">{{ number_format($totalBorrowed) }}</p>
            <p class="text-xs text-gray-500">Dipinjam</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center hover:shadow-md transition">
            <p class="text-2xl font-bold text-rose-500">{{ number_format($totalMaintenance) }}</p>
            <p class="text-xs text-gray-500">Perbaikan</p>
        </div>
    </div>

    {{-- FILTER & SEARCH --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari kode, nama, atau lokasi..." 
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition text-sm">
            </div>
            <div>
                <select name="unit" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition text-sm bg-white">
                    <option value="">Semua Unit</option>
                    @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ request('unit') == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition text-sm bg-white">
                    <option value="">Semua Status</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                    <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Perbaikan</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition flex items-center justify-center gap-2 flex-1">
                    <i class="fas fa-search text-xs"></i>
                    Cari
                </button>
                @if(request('search') || request('unit') || request('status'))
                <a href="{{ route('manager.items.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-medium transition flex items-center justify-center">
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
                        <th class="px-4 sm:px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-4 sm:px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-4 sm:px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-4 sm:px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-4 sm:px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th class="px-4 sm:px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 sm:px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($items as $index => $item)
                    <tr class="hover:bg-brand-50/30 transition">
                        <td class="px-4 sm:px-6 py-4 text-sm text-gray-500">{{ $items->firstItem() + $index }}</td>
                        <td class="px-4 sm:px-6 py-4">
                            <span class="text-sm font-mono font-medium text-brand-700 bg-brand-50 px-2 py-1 rounded-lg">{{ $item->code }}</span>
                        </td>
                        <td class="px-4 sm:px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-9 h-9 rounded-lg object-cover border border-gray-100">
                                @else
                                    <div class="w-9 h-9 rounded-lg bg-brand-50 flex items-center justify-center border border-gray-100">
                                        <i class="fas fa-box text-brand-400 text-sm"></i>
                                    </div>
                                @endif
                                <span class="text-sm font-medium text-gray-800">{{ $item->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">{{ $item->unit->name ?? '-' }}</td>
                        <td class="px-4 sm:px-6 py-4 text-sm text-gray-600">{{ $item->location ?? '-' }}</td>
                        <td class="px-4 sm:px-6 py-4">
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full inline-flex items-center gap-1.5
                                {{ $item->status == 'available' ? 'bg-emerald-50 text-emerald-700' : 
                                   ($item->status == 'borrowed' ? 'bg-brand-50 text-brand-700' : 'bg-amber-50 text-amber-700') }}">
                                <span class="w-1.5 h-1.5 rounded-full
                                    {{ $item->status == 'available' ? 'bg-emerald-500' : 
                                       ($item->status == 'borrowed' ? 'bg-brand-500' : 'bg-amber-500') }}"></span>
                                {{ $item->status == 'available' ? 'Tersedia' : ($item->status == 'borrowed' ? 'Dipinjam' : 'Perbaikan') }}
                            </span>
                        </td>
                        <td class="px-4 sm:px-6 py-4 text-center">
                            <a href="{{ route('manager.items.show', $item) }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-600 hover:text-brand-700 transition text-xs font-medium"
                               title="Lihat Detail">
                                <i class="fas fa-eye text-sm"></i>
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 sm:px-6 py-16 text-center text-gray-400">
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
    /* ===== WARNA TOSKA ===== */
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

    /* ===== TRANSISI ===== */
    .transition { transition: all 0.2s ease; }

    /* ===== PAGINATION ===== */
    .pagination {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }
    .pagination .page-link {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 13px;
        color: #6b7280;
        background: transparent;
        transition: all 0.2s;
    }
    .pagination .page-link:hover {
        background: #f0fdfa;
        color: #0d9488;
    }
    .pagination .active .page-link {
        background: #0d9488;
        color: white;
    }
    .pagination .disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* ===== SCROLLBAR ===== */
    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
    }
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #ccfbf1;
        border-radius: 10px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #2dd4bf;
    }
</style>
@endpush
@endsection