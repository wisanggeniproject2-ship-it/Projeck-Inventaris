@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-6xl px-3 sm:px-4 lg:px-6 py-4 sm:py-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-5">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                <i class="fas fa-trash-can text-red-500 mr-2"></i>Pengajuan Penghapusan Aset
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola pengajuan penghapusan barang rusak</p>
        </div>
    </div>

    {{-- FILTER STATUS --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-3 sm:p-4 mb-5">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('super_admin.disposals.index') }}"
               class="px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition
                      {{ !request('status') 
                            ? 'bg-gradient-to-r from-brand-500 to-brand-600 text-white shadow-sm' 
                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                <i class="fas fa-list mr-1"></i>
                Semua
            </a>
            <a href="{{ route('super_admin.disposals.index', ['status' => 'pending']) }}"
               class="px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition
                      {{ request('status') == 'pending' 
                            ? 'bg-gradient-to-r from-yellow-500 to-yellow-600 text-white shadow-sm' 
                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                <i class="fas fa-clock mr-1"></i>
                Menunggu
            </a>
            <a href="{{ route('super_admin.disposals.index', ['status' => 'approved']) }}"
               class="px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition
                      {{ request('status') == 'approved' 
                            ? 'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-sm' 
                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                <i class="fas fa-check-circle mr-1"></i>
                Disetujui
            </a>
            <a href="{{ route('super_admin.disposals.index', ['status' => 'rejected']) }}"
               class="px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition
                      {{ request('status') == 'rejected' 
                            ? 'bg-gradient-to-r from-gray-500 to-gray-600 text-white shadow-sm' 
                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                <i class="fas fa-times-circle mr-1"></i>
                Ditolak
            </a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 📱 MOBILE VIEW (≤ md) — Card per disposal                    --}}
    {{-- ============================================================ --}}
    <div class="block md:hidden space-y-3">
        @forelse($disposals as $disposal)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Header: Barang + Status --}}
            <div class="p-3 border-b border-gray-100">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-sm text-gray-800 truncate">{{ $disposal->item->name ?? '-' }}</p>
                    </div>
                    @php
                        $statusClasses = [
                            'pending'  => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                            'approved' => 'bg-red-100 text-red-700 border-red-200',
                            'rejected' => 'bg-gray-100 text-gray-600 border-gray-200',
                        ];
                        $statusLabels = [
                            'pending'  => 'Menunggu',
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak',
                        ];
                        $cls = $statusClasses[$disposal->status] ?? 'bg-gray-100 text-gray-600';
                        $lbl = $statusLabels[$disposal->status] ?? ucfirst($disposal->status);
                    @endphp
                    <span class="px-2 py-1 text-[10px] font-bold rounded-full border shrink-0 {{ $cls }}">
                        {{ $lbl }}
                    </span>
                </div>

                {{-- 🔥 Kode stok spesifik --}}
                @if($disposal->stock_code)
                    <div class="inline-flex items-start gap-1 px-2 py-1 rounded-md border w-full"
                         style="background: #0F6B5F10; border-color: #0F6B5F30;">
                        <i class="fas fa-qrcode text-[9px] mt-0.5 shrink-0" style="color: #0F6B5F;"></i>
                        <span class="font-mono text-[9px] font-semibold leading-tight break-all"
                              style="color: #0F6B5F;">
                            {{ $disposal->stock_code }}
                        </span>
                    </div>
                @else
                    <div class="inline-flex items-start gap-1 px-2 py-1 rounded-md border border-gray-200 bg-gray-50">
                        <i class="fas fa-question-circle text-[9px] mt-0.5 text-gray-400"></i>
                        <span class="text-[9px] text-gray-500 italic">Kode stok tidak tersedia</span>
                    </div>
                @endif
            </div>

            {{-- Body: Info --}}
            <div class="grid grid-cols-2 gap-2 p-3 bg-gray-50 border-b border-gray-100">
                <div>
                    <p class="text-[9px] text-gray-500 uppercase font-semibold">Diajukan Oleh</p>
                    <p class="text-[11px] font-medium text-gray-800 mt-0.5 truncate">
                        <i class="fas fa-user text-[9px] text-gray-400 mr-1"></i>
                        {{ $disposal->user->name ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-[9px] text-gray-500 uppercase font-semibold">Tanggal</p>
                    <p class="text-[11px] font-medium text-gray-800 mt-0.5">
                        <i class="fas fa-calendar text-[9px] text-gray-400 mr-1"></i>
                        {{ $disposal->created_at->format('d/m/Y') }}
                    </p>
                    <p class="text-[10px] text-gray-500">
                        <i class="fas fa-clock text-[9px] mr-1"></i>
                        {{ $disposal->created_at->format('H:i') }} WIB
                    </p>
                </div>
            </div>

            {{-- Alasan --}}
            <div class="p-3 border-b border-gray-100">
                <p class="text-[9px] text-gray-500 uppercase font-semibold mb-1">Alasan</p>
                <p class="text-[11px] text-gray-700 line-clamp-3 leading-snug">{{ $disposal->reason }}</p>
            </div>

            {{-- Aksi --}}
            <div class="p-2 bg-white">
                <a href="{{ route('super_admin.disposals.show', $disposal) }}"
                   class="flex items-center justify-center gap-2 py-2.5 rounded-xl text-brand-600 bg-brand-50 hover:bg-brand-100 transition text-xs font-semibold border border-brand-100">
                    <i class="fas fa-eye"></i>
                    <span>Lihat Detail</span>
                </a>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl shadow-sm p-8 text-center border border-gray-100">
            <i class="fas fa-inbox text-4xl text-gray-300 block mb-3"></i>
            <p class="text-gray-600 font-medium text-sm mb-1">Belum ada pengajuan</p>
            <p class="text-xs text-gray-400">Pengajuan penghapusan aset akan muncul di sini</p>
        </div>
        @endforelse
    </div>

    {{-- ============================================================ --}}
    {{-- 💻 TABLET & DESKTOP (≥ md) — Tabel                            --}}
    {{-- ============================================================ --}}
    <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 lg:px-4 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Barang</th>
                        <th class="px-3 lg:px-4 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Kode Stok</th>
                        <th class="px-3 lg:px-4 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Pemohon</th>
                        <th class="px-3 lg:px-4 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Alasan</th>
                        <th class="px-3 lg:px-4 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-3 lg:px-4 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-3 lg:px-4 py-3 text-left text-[10px] lg:text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($disposals as $disposal)
                    <tr class="hover:bg-gray-50 transition">
                        {{-- Barang --}}
                        <td class="px-3 lg:px-4 py-3">
                            <p class="font-medium text-xs lg:text-sm text-gray-800">{{ $disposal->item->name ?? '-' }}</p>
                        </td>

                        {{-- 🔥 Kode Stok --}}
                        <td class="px-3 lg:px-4 py-3 max-w-[220px]">
                            @if($disposal->stock_code)
                                <div class="inline-flex items-start gap-1 px-2 py-1 rounded-md border"
                                     style="background: #0F6B5F10; border-color: #0F6B5F30;">
                                    <i class="fas fa-qrcode text-[9px] mt-0.5 shrink-0" style="color: #0F6B5F;"></i>
                                    <span class="font-mono text-[9px] lg:text-[10px] font-semibold leading-tight break-all"
                                          style="color: #0F6B5F;">
                                        {{ $disposal->stock_code }}
                                    </span>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs italic">-</span>
                            @endif
                        </td>

                        {{-- Pemohon --}}
                        <td class="px-3 lg:px-4 py-3 text-xs lg:text-sm text-gray-700 whitespace-nowrap">
                            {{ $disposal->user->name ?? '-' }}
                        </td>

                        {{-- Alasan --}}
                        <td class="px-3 lg:px-4 py-3 text-xs lg:text-sm text-gray-600 max-w-[200px]">
                            <span class="line-clamp-2">{{ $disposal->reason }}</span>
                        </td>

                        {{-- Tanggal --}}
                        <td class="px-3 lg:px-4 py-3 text-xs lg:text-sm text-gray-500 whitespace-nowrap">
                            {{ $disposal->created_at->format('d/m/Y') }}
                            <br>
                            <span class="text-[10px] text-gray-400">{{ $disposal->created_at->format('H:i') }}</span>
                        </td>

                        {{-- Status --}}
                        <td class="px-3 lg:px-4 py-3 whitespace-nowrap">
                            <span class="px-2.5 py-1 text-[10px] lg:text-xs font-medium rounded-full
                                {{ $disposal->status == 'pending' ? 'bg-yellow-100 text-yellow-700' :
                                   ($disposal->status == 'approved' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                                @if($disposal->status == 'pending') ⏳
                                @elseif($disposal->status == 'approved') ✅
                                @elseif($disposal->status == 'rejected') ❌
                                @endif
                                {{ $disposal->status == 'pending' ? 'Menunggu' :
                                   ($disposal->status == 'approved' ? 'Disetujui' : 'Ditolak') }}
                            </span>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-3 lg:px-4 py-3 whitespace-nowrap">
                            <a href="{{ route('super_admin.disposals.show', $disposal) }}"
                               class="inline-flex items-center gap-1 text-brand-600 hover:text-brand-800 text-xs lg:text-sm font-medium transition">
                                <i class="fas fa-eye"></i>
                                <span class="hidden lg:inline">Detail</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl text-gray-300 block mb-3"></i>
                            <p class="font-medium text-sm mb-1">Belum ada pengajuan penghapusan aset</p>
                            <p class="text-xs text-gray-400">Pengajuan akan muncul di sini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    @if($disposals->hasPages())
    <div class="mt-5">
        {{ $disposals->withQueryString()->links() }}
    </div>
    @endif

</div>
@endsection