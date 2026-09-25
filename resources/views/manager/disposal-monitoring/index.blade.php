@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-trash-can text-red-600 mr-2"></i>Monitoring Penghapusan Aset
            </h1>
            <p class="text-sm text-gray-500 mt-1">Lihat riwayat penghapusan aset inventaris (mode monitoring)</p>
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-500 bg-red-50 px-4 py-2 rounded-xl border border-red-100">
            <i class="fas fa-eye text-red-500"></i>
            <span>Hanya Monitoring</span>
        </div>
    </div>

    {{-- STATS RINGKASAN --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
        {{-- Total --}}
        <div class="bg-white rounded-xl shadow-sm border-2 border-gray-100 p-4 text-center hover:shadow-md transition">
            <div class="w-10 h-10 mx-auto rounded-lg bg-gray-100 flex items-center justify-center mb-2">
                <i class="fas fa-list text-gray-600"></i>
            </div>
            <p class="text-2xl font-bold text-gray-700">{{ number_format($stats['total']) }}</p>
            <p class="text-[10px] text-gray-500 uppercase font-semibold">Total</p>
        </div>

        {{-- Pending --}}
        <div class="bg-white rounded-xl shadow-sm border-2 border-yellow-100 p-4 text-center hover:shadow-md transition">
            <div class="w-10 h-10 mx-auto rounded-lg bg-yellow-50 flex items-center justify-center mb-2">
                <i class="fas fa-clock text-yellow-600"></i>
            </div>
            <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['pending']) }}</p>
            <p class="text-[10px] text-gray-500 uppercase font-semibold">Menunggu</p>
        </div>

        {{-- Approved --}}
        <div class="bg-white rounded-xl shadow-sm border-2 border-red-100 p-4 text-center hover:shadow-md transition">
            <div class="w-10 h-10 mx-auto rounded-lg bg-red-50 flex items-center justify-center mb-2">
                <i class="fas fa-check-circle text-red-600"></i>
            </div>
            <p class="text-2xl font-bold text-red-600">{{ number_format($stats['approved']) }}</p>
            <p class="text-[10px] text-gray-500 uppercase font-semibold">Disetujui</p>
        </div>

        {{-- Rejected --}}
        <div class="bg-white rounded-xl shadow-sm border-2 border-gray-100 p-4 text-center hover:shadow-md transition">
            <div class="w-10 h-10 mx-auto rounded-lg bg-gray-100 flex items-center justify-center mb-2">
                <i class="fas fa-times-circle text-gray-600"></i>
            </div>
            <p class="text-2xl font-bold text-gray-600">{{ number_format($stats['rejected']) }}</p>
            <p class="text-[10px] text-gray-500 uppercase font-semibold">Ditolak</p>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            {{-- Search --}}
            <div class="relative lg:col-span-2">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari kode stok, nama barang, alasan, pengaju..."
                       class="w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition text-sm">
            </div>

            {{-- Status --}}
            <div>
                <select name="status" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500">
                    <option value="">📋 Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Menunggu</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Disetujui</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Ditolak</option>
                </select>
            </div>

            {{-- Unit --}}
            <div>
                <select name="unit" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500">
                    <option value="">🏢 Semua Unit</option>
                    @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ request('unit') == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Button --}}
            <div class="flex gap-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition flex items-center justify-center gap-2 flex-1">
                    <i class="fas fa-search text-xs"></i>
                    Cari
                </button>
                @if(request()->anyFilled(['search', 'status', 'unit']))
                <a href="{{ route('manager.disposal-monitoring.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-medium transition flex items-center justify-center" title="Reset">
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
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">No</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Barang</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Kode Stok</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Unit</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Diajukan Oleh</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Tanggal Ajuan</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Status</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($disposals as $index => $disposal)
                    @php
                        $statusConfig = [
                            'pending'  => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => 'fa-clock', 'label' => 'Menunggu'],
                            'approved' => ['bg' => 'bg-red-100',    'text' => 'text-red-700',    'icon' => 'fa-check-circle', 'label' => 'Disetujui'],
                            'rejected' => ['bg' => 'bg-gray-100',   'text' => 'text-gray-600',   'icon' => 'fa-times-circle', 'label' => 'Ditolak'],
                        ];
                        $sc = $statusConfig[$disposal->status] ?? $statusConfig['pending'];
                        
                        // 🔥 Cek foto bukti
                        $photos = is_array($disposal->photos) ? $disposal->photos : [];
                        $photoCount = count($photos);
                    @endphp
                    <tr class="hover:bg-gray-50/70 transition align-top">
                        {{-- No --}}
                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                            {{ $disposals->firstItem() + $index }}
                        </td>

                        {{-- Barang --}}
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                @if($disposal->item && $disposal->item->image)
                                    <img src="{{ $disposal->item->image_url }}" alt="{{ $disposal->item->name }}"
                                         class="w-9 h-9 rounded-lg object-cover border border-gray-100 shrink-0">
                                @else
                                    <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-100 shrink-0">
                                        <i class="fas fa-box text-gray-400 text-sm"></i>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">
                                        {{ $disposal->item->name ?? '-' }}
                                    </p>
                                    @if($disposal->item && $disposal->item->category)
                                    <span class="inline-flex items-center gap-1 text-[9px] font-medium bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded border border-blue-100 mt-0.5">
                                        <i class="fas fa-tag text-[8px]"></i>{{ $disposal->item->category->name }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Kode Stok --}}
                        <td class="px-4 py-4 max-w-[200px]">
                            @if($disposal->stock_code)
                                <div class="inline-flex items-start gap-1 px-2 py-1 rounded-md text-[10px] font-mono font-bold break-all"
                                     style="background: #0F6B5F15; color: #0F6B5F;">
                                    <i class="fas fa-barcode text-[8px] mt-0.5 shrink-0"></i>
                                    {{ $disposal->stock_code }}
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">-</span>
                            @endif
                        </td>

                        {{-- Unit --}}
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 text-[10px] font-medium bg-teal-50 text-teal-700 px-2 py-1 rounded-lg border border-teal-100">
                                <i class="fas fa-building text-[9px]"></i>
                                {{ $disposal->item->unit->name ?? '-' }}
                            </span>
                        </td>

                        {{-- Diajukan Oleh --}}
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-1.5">
                                <div class="w-6 h-6 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white text-[9px] font-bold shrink-0">
                                    {{ strtoupper(substr($disposal->user->name ?? '?', 0, 1)) }}
                                </div>
                                <span class="text-xs text-gray-700">{{ $disposal->user->name ?? '-' }}</span>
                            </div>
                        </td>

                        {{-- Tanggal --}}
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-xs text-gray-700">
                                <i class="fas fa-calendar-alt text-gray-400 text-[10px] mr-1"></i>
                                {{ $disposal->created_at->format('d/m/Y') }}
                            </div>
                            <div class="text-[10px] text-gray-500 mt-0.5">
                                <i class="fas fa-clock text-gray-400 text-[9px] mr-1"></i>
                                {{ $disposal->created_at->format('H:i') }} WIB
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full {{ $sc['bg'] }} {{ $sc['text'] }}">
                                <i class="fas {{ $sc['icon'] }} text-[10px]"></i>
                                {{ $sc['label'] }}
                            </span>

                            {{-- Info foto --}}
                            @if($photoCount > 0)
                            <div class="mt-1 inline-flex items-center gap-1 text-[10px] text-gray-500">
                                <i class="fas fa-camera text-[9px]"></i>
                                {{ $photoCount }} foto
                            </div>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <a href="{{ route('manager.disposal-monitoring.show', $disposal) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-600 hover:text-brand-700 transition text-xs font-medium"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                                <span class="hidden sm:inline">Detail</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                    <i class="fas fa-trash-can text-3xl text-gray-300"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-600">Belum ada data penghapusan aset</p>
                                <p class="text-xs text-gray-400 mt-1">Riwayat penghapusan akan muncul di sini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER --}}
        @if($disposals->hasPages())
        <div class="px-4 sm:px-6 py-3 border-t border-gray-100 flex justify-between items-center gap-3 bg-gray-50/30">
            <div class="text-xs text-gray-500">
                Menampilkan <span class="font-medium text-gray-700">{{ $disposals->firstItem() ?? 0 }}</span>
                - <span class="font-medium text-gray-700">{{ $disposals->lastItem() ?? 0 }}</span>
                dari <span class="font-medium text-gray-700">{{ $disposals->total() }}</span> data
            </div>
            <div>
                {{ $disposals->withQueryString()->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection