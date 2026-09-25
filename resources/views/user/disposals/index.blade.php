@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-5xl px-3 sm:px-4 lg:px-6 py-4 sm:py-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-5 sm:mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                <i class="fas fa-trash-alt text-red-500 mr-2"></i>Riwayat Pengajuan Penghapusan
            </h1>
            <p class="text-sm text-gray-500 mt-1">Daftar semua pengajuan penghapusan aset yang pernah Anda buat</p>
        </div>
        <a href="{{ route('user.disposals.create') }}" 
           class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-2.5 rounded-xl transition-all text-sm font-medium shadow-sm hover:shadow-md inline-flex items-center justify-center gap-2 w-full sm:w-auto">
            <i class="fas fa-plus"></i>
            <span>Ajukan Baru</span>
        </a>
    </div>

    {{-- STATS RINGKASAN --}}
    @php
        $totalCount    = $disposals->total() ?? 0;
        $pendingCount  = $disposals->where('status', 'pending')->count();
        $approvedCount = $disposals->where('status', 'approved')->count();
        $rejectedCount = $disposals->where('status', 'rejected')->count();
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3 mb-5">
        {{-- Total --}}
        <div class="bg-white rounded-xl shadow-sm p-3 border-2 border-gray-100 hover:border-gray-300 transition">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-list text-gray-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-700 leading-none">{{ $totalCount }}</p>
                    <p class="text-[10px] text-gray-500 uppercase mt-0.5">Total</p>
                </div>
            </div>
        </div>

        {{-- Pending --}}
        <div class="bg-white rounded-xl shadow-sm p-3 border-2 border-yellow-100 hover:border-yellow-300 transition">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-lg bg-yellow-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-clock text-yellow-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-yellow-600 leading-none">{{ $pendingCount }}</p>
                    <p class="text-[10px] text-gray-500 uppercase mt-0.5">Menunggu</p>
                </div>
            </div>
        </div>

        {{-- Approved --}}
        <div class="bg-white rounded-xl shadow-sm p-3 border-2 border-red-100 hover:border-red-300 transition">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-check-circle text-red-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-red-600 leading-none">{{ $approvedCount }}</p>
                    <p class="text-[10px] text-gray-500 uppercase mt-0.5">Disetujui</p>
                </div>
            </div>
        </div>

        {{-- Rejected --}}
        <div class="bg-white rounded-xl shadow-sm p-3 border-2 border-gray-100 hover:border-gray-300 transition">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-times-circle text-gray-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-600 leading-none">{{ $rejectedCount }}</p>
                    <p class="text-[10px] text-gray-500 uppercase mt-0.5">Ditolak</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 📱 MOBILE VIEW — Card Layout                                --}}
    {{-- ============================================================ --}}
    <div class="block md:hidden space-y-3">
        @forelse($disposals as $disposal)
        @php
            $statusConfig = [
                'pending'  => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200', 'icon' => 'fa-clock', 'label' => 'Menunggu'],
                'approved' => ['bg' => 'bg-red-100',    'text' => 'text-red-700',    'border' => 'border-red-200',    'icon' => 'fa-check-circle', 'label' => 'Disetujui'],
                'rejected' => ['bg' => 'bg-gray-100',   'text' => 'text-gray-600',   'border' => 'border-gray-200',   'icon' => 'fa-times-circle', 'label' => 'Ditolak'],
            ];
            $sc = $statusConfig[$disposal->status] ?? $statusConfig['pending'];
        @endphp

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
            {{-- Header Card --}}
            <div class="p-4 border-b border-gray-100">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <h3 class="font-bold text-sm text-gray-800 flex-1 min-w-0 line-clamp-2">
                        {{ $disposal->item->name ?? 'Barang tidak ditemukan' }}
                    </h3>
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-semibold rounded-full {{ $sc['bg'] }} {{ $sc['text'] }} border {{ $sc['border'] }} shrink-0">
                        <i class="fas {{ $sc['icon'] }} text-[9px]"></i>
                        {{ $sc['label'] }}
                    </span>
                </div>

                {{-- 🔥 Kode Stok Spesifik --}}
                @if($disposal->stock_code)
                    <div class="inline-flex items-start gap-1 px-2 py-1 rounded-md border max-w-full"
                         style="background: #0F6B5F10; border-color: #0F6B5F30;">
                        <i class="fas fa-barcode text-[9px] mt-0.5 shrink-0" style="color: #0F6B5F;"></i>
                        <span class="font-mono text-[9px] font-semibold leading-tight break-all"
                              style="color: #0F6B5F;">
                            {{ $disposal->stock_code }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Body --}}
            <div class="p-4 space-y-3">
                {{-- Alasan --}}
                <div>
                    <p class="text-[10px] font-semibold text-gray-500 uppercase mb-1">
                        <i class="fas fa-comment-alt mr-1"></i>Alasan Pengajuan
                    </p>
                    <p class="text-sm text-gray-700 leading-snug">{{ $disposal->reason }}</p>
                </div>

                {{-- Tanggal --}}
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <i class="fas fa-calendar-alt text-gray-400"></i>
                    <span>Diajukan: {{ $disposal->created_at->format('d/m/Y H:i') }} WIB</span>
                </div>

                {{-- Catatan Admin (kalau ada) --}}
                @if($disposal->rejection_reason)
                    <div class="p-3 bg-red-50 border-l-4 border-red-400 rounded-r-lg">
                        <p class="text-[10px] font-semibold text-red-700 uppercase mb-1">
                            <i class="fas fa-info-circle mr-1"></i>Catatan Admin
                        </p>
                        <p class="text-xs text-red-700 leading-snug">{{ $disposal->rejection_reason }}</p>
                    </div>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl shadow-sm p-8 text-center border border-gray-100">
            <div class="w-20 h-20 mx-auto mb-3 rounded-full bg-gray-100 flex items-center justify-center">
                <i class="fas fa-inbox text-3xl text-gray-300"></i>
            </div>
            <p class="text-gray-600 font-medium mb-1">Belum ada pengajuan penghapusan</p>
            <p class="text-xs text-gray-400 mb-4">Ajukan penghapusan aset yang sudah tidak layak pakai</p>
            <a href="{{ route('user.disposals.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl text-sm font-medium transition shadow-sm hover:shadow-md">
                <i class="fas fa-plus"></i>
                Ajukan Sekarang
            </a>
        </div>
        @endforelse
    </div>

    {{-- ============================================================ --}}
    {{-- 💻 DESKTOP VIEW — Tabel                                     --}}
    {{-- ============================================================ --}}
    <div class="hidden md:block bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 lg:px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-box text-gray-400 mr-1"></i>Barang
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-qrcode text-gray-400 mr-1"></i>Kode Stok
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-comment-alt text-gray-400 mr-1"></i>Alasan
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-calendar text-gray-400 mr-1"></i>Tanggal Ajuan
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-info-circle text-gray-400 mr-1"></i>Status
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-reply text-gray-400 mr-1"></i>Catatan Admin
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($disposals as $disposal)
                    @php
                        $statusConfig = [
                            'pending'  => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200', 'icon' => 'fa-clock', 'label' => 'Menunggu Konfirmasi'],
                            'approved' => ['bg' => 'bg-red-100',    'text' => 'text-red-700',    'border' => 'border-red-200',    'icon' => 'fa-check-circle', 'label' => 'Disetujui (Dihapus)'],
                            'rejected' => ['bg' => 'bg-gray-100',   'text' => 'text-gray-600',   'border' => 'border-gray-200',   'icon' => 'fa-times-circle', 'label' => 'Ditolak'],
                        ];
                        $sc = $statusConfig[$disposal->status] ?? $statusConfig['pending'];
                    @endphp
                    <tr class="hover:bg-gray-50/70 transition
                        {{ $disposal->status == 'pending' ? 'bg-yellow-50/40' : '' }}
                        {{ $disposal->status == 'approved' ? 'bg-red-50/30' : '' }}">

                        {{-- BARANG --}}
                        <td class="px-4 lg:px-6 py-4">
                            <div class="text-sm font-medium text-gray-800">
                                {{ $disposal->item->name ?? 'Barang tidak ditemukan' }}
                            </div>

                            {{-- Kategori/Unit --}}
                            <div class="flex flex-wrap gap-1.5 mt-1.5">
                                @if($disposal->item->category ?? false)
                                    <span class="inline-flex items-center gap-1 text-[9px] font-medium bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded border border-blue-100">
                                        <i class="fas fa-tag text-[8px]"></i>{{ $disposal->item->category->name }}
                                    </span>
                                @endif
                                @if($disposal->item->unit ?? false)
                                    <span class="inline-flex items-center gap-1 text-[9px] font-medium bg-teal-50 text-teal-700 px-1.5 py-0.5 rounded border border-teal-100">
                                        <i class="fas fa-building text-[8px]"></i>{{ $disposal->item->unit->name }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- 🔥 KODE STOK --}}
                        <td class="px-4 lg:px-6 py-4 max-w-[240px]">
                            @if($disposal->stock_code)
                                <div class="inline-flex items-start gap-1 px-2 py-1 rounded-md border"
                                     style="background: #0F6B5F10; border-color: #0F6B5F30;">
                                    <i class="fas fa-barcode text-[9px] mt-0.5 shrink-0" style="color: #0F6B5F;"></i>
                                    <span class="font-mono text-[10px] font-semibold leading-tight break-all"
                                          style="color: #0F6B5F;">
                                        {{ $disposal->stock_code }}
                                    </span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">-</span>
                            @endif
                        </td>

                        {{-- ALASAN --}}
                        <td class="px-4 lg:px-6 py-4 max-w-xs">
                            <p class="text-sm text-gray-700 leading-snug line-clamp-3">
                                {{ $disposal->reason }}
                            </p>
                        </td>

                        {{-- TANGGAL --}}
                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-700">
                                <i class="fas fa-calendar-alt text-gray-400 text-xs mr-1"></i>
                                {{ $disposal->created_at->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-500 mt-0.5">
                                <i class="fas fa-clock text-gray-400 text-[10px] mr-1"></i>
                                {{ $disposal->created_at->format('H:i') }} WIB
                            </div>
                        </td>

                        {{-- STATUS --}}
                        <td class="px-4 lg:px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full {{ $sc['bg'] }} {{ $sc['text'] }} border {{ $sc['border'] }}">
                                <i class="fas {{ $sc['icon'] }} text-[10px]"></i>
                                {{ $sc['label'] }}
                            </span>

                            {{-- Info tambahan --}}
                            @if($disposal->status == 'approved' && $disposal->approved_at ?? false)
                                <p class="text-[10px] text-gray-400 mt-1">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $disposal->approved_at->format('d/m/Y H:i') }}
                                </p>
                            @elseif($disposal->status == 'rejected' && $disposal->rejected_at ?? false)
                                <p class="text-[10px] text-gray-400 mt-1">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $disposal->rejected_at->format('d/m/Y H:i') }}
                                </p>
                            @endif
                        </td>

                        {{-- CATATAN ADMIN --}}
                        <td class="px-4 lg:px-6 py-4 max-w-xs">
                            @if($disposal->rejection_reason)
                                <div class="p-2 bg-red-50 border-l-4 border-red-400 rounded-r text-xs text-red-700">
                                    <p class="break-words leading-snug">{{ $disposal->rejection_reason }}</p>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <div class="w-20 h-20 mx-auto mb-3 rounded-full bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-inbox text-3xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-600 font-medium mb-1">Belum ada pengajuan penghapusan</p>
                            <p class="text-xs text-gray-400 mb-4">Ajukan penghapusan aset yang sudah tidak layak pakai</p>
                            <a href="{{ route('user.disposals.create') }}" 
                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl text-sm font-medium transition shadow-sm hover:shadow-md">
                                <i class="fas fa-plus"></i>
                                Ajukan Sekarang
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    @if($disposals->hasPages())
    <div class="mt-5 sm:mt-6">
        {{ $disposals->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection