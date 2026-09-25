@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-3xl px-3 sm:px-4 lg:px-6 py-4 sm:py-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-5">
        <div>
            <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">
                <i class="fas fa-trash-can text-red-500 mr-2"></i>Detail Pengajuan
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Pengajuan penghapusan aset #{{ $disposal->id }}</p>
        </div>
        <a href="{{ route('super_admin.disposals.index') }}" 
           class="text-gray-600 hover:text-gray-800 text-xs sm:text-sm inline-flex items-center gap-1 bg-white border border-gray-200 px-3 py-2 rounded-xl hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    <div class="space-y-4">

        {{-- ============================================================ --}}
        {{-- INFO BARANG + KODE STOK                                      --}}
        {{-- ============================================================ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <div class="flex items-start gap-3 mb-4 pb-4 border-b border-gray-100">
                <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-box text-red-600"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs text-gray-500 uppercase font-semibold">Barang yang Diajukan</p>
                    <p class="font-bold text-base sm:text-lg text-gray-800 mt-0.5">{{ $disposal->item->name ?? '-' }}</p>
                    <p class="text-xs text-gray-500 font-mono mt-0.5">{{ $disposal->item->code ?? '-' }}</p>
                </div>
            </div>

            {{-- 🔥 Kode Stok Spesifik --}}
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold mb-2">
                    <i class="fas fa-qrcode mr-1 text-brand-500"></i>
                    Kode Stok yang Diajukan
                </p>

                @if($disposal->stock_code)
                    <div class="flex items-start gap-2 p-3 rounded-xl border-2"
                         style="background: #0F6B5F10; border-color: #0F6B5F40;">
                        <i class="fas fa-qrcode text-sm mt-0.5 shrink-0" style="color: #0F6B5F;"></i>
                        <div class="min-w-0 flex-1">
                            <p class="font-mono text-xs sm:text-sm font-bold break-all leading-relaxed"
                               style="color: #0F6B5F;">
                                {{ $disposal->stock_code }}
                            </p>
                            @if($disposal->itemStock)
                                <p class="text-[10px] text-gray-500 mt-1">
                                    Status: 
                                    <span class="px-1.5 py-0.5 text-[9px] font-bold rounded-full
                                        {{ $disposal->itemStock->status === 'available' ? 'bg-green-100 text-green-700' :
                                           ($disposal->itemStock->status === 'disposed' ? 'bg-red-100 text-red-700' : 
                                           'bg-gray-100 text-gray-600') }}">
                                        {{ ucfirst($disposal->itemStock->status) }}
                                    </span>
                                    • Stok ke-{{ $disposal->itemStock->stock_number }}
                                </p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="p-3 rounded-xl border-2 border-gray-200 bg-gray-50 text-center">
                        <p class="text-xs text-gray-500 italic">
                            <i class="fas fa-question-circle mr-1"></i>
                            Kode stok tidak tersedia (pengajuan lama)
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- INFO PENGAJUAN                                                --}}
        {{-- ============================================================ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Diajukan Oleh --}}
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold mb-1.5">
                        <i class="fas fa-user mr-1 text-brand-500"></i>Diajukan Oleh
                    </p>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 text-xs font-bold shrink-0">
                            {{ strtoupper(substr($disposal->user->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-medium text-sm text-gray-800 truncate">{{ $disposal->user->name ?? '-' }}</p>
                            <p class="text-[10px] text-gray-500 truncate">{{ $disposal->user->unit->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Tanggal --}}
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold mb-1.5">
                        <i class="fas fa-calendar mr-1 text-brand-500"></i>Tanggal Pengajuan
                    </p>
                    <p class="font-medium text-sm text-gray-800">{{ $disposal->created_at->format('d F Y') }}</p>
                    <p class="text-xs text-gray-500">
                        <i class="fas fa-clock text-[10px] mr-1"></i>
                        {{ $disposal->created_at->format('H:i') }} WIB
                    </p>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- ALASAN PENGHAPUSAN                                           --}}
        {{-- ============================================================ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <p class="text-xs text-gray-500 uppercase font-semibold mb-2">
                <i class="fas fa-clipboard-list mr-1 text-brand-500"></i>Alasan Penghapusan
            </p>
            <div class="bg-gray-50 rounded-xl p-3 sm:p-4 border border-gray-100">
                <p class="text-sm text-gray-700 leading-relaxed">{{ $disposal->reason }}</p>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- FOTO BUKTI                                                    --}}
        {{-- ============================================================ --}}
        @if($disposal->photos && count($disposal->photos) > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <p class="text-xs text-gray-500 uppercase font-semibold mb-3">
                <i class="fas fa-camera mr-1 text-brand-500"></i>
                Foto Bukti Kerusakan ({{ count($disposal->photos) }})
            </p>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 sm:gap-3">
                @foreach($disposal->photos as $index => $photo)
                <a href="{{ \Storage::url($photo) }}" target="_blank" 
                   class="group relative aspect-square rounded-xl overflow-hidden border-2 border-gray-200 hover:border-brand-400 transition">
                    <img src="{{ \Storage::url($photo) }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    <div class="absolute top-2 right-2 bg-black/60 text-white text-[10px] font-bold px-2 py-0.5 rounded-md">
                        {{ $index + 1 }}
                    </div>
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition flex items-center justify-center">
                        <i class="fas fa-search-plus text-white text-2xl opacity-0 group-hover:opacity-100 transition"></i>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ============================================================ --}}
        {{-- STATUS                                                        --}}
        {{-- ============================================================ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <p class="text-xs text-gray-500 uppercase font-semibold mb-3">
                <i class="fas fa-info-circle mr-1 text-brand-500"></i>Status Pengajuan
            </p>

            @php
                $statusConfig = [
                    'pending'  => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'text' => 'text-yellow-700', 'icon' => 'fa-clock', 'label' => 'Menunggu Konfirmasi'],
                    'approved' => ['bg' => 'bg-red-50',    'border' => 'border-red-200',    'text' => 'text-red-700',    'icon' => 'fa-circle-check', 'label' => 'Disetujui'],
                    'rejected' => ['bg' => 'bg-gray-50',   'border' => 'border-gray-200',   'text' => 'text-gray-700',   'icon' => 'fa-circle-xmark', 'label' => 'Ditolak'],
                ];
                $cfg = $statusConfig[$disposal->status] ?? $statusConfig['pending'];
            @endphp

            <div class="{{ $cfg['bg'] }} {{ $cfg['border'] }} border-2 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center shrink-0">
                        <i class="fas {{ $cfg['icon'] }} {{ $cfg['text'] }} text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-bold {{ $cfg['text'] }} text-sm">{{ $cfg['label'] }}</p>

                        @if($disposal->status == 'approved' && $disposal->approver)
                            <p class="text-xs text-gray-600 mt-1">
                                Disetujui oleh <strong>{{ $disposal->approver->name }}</strong>
                            </p>
                            <p class="text-[11px] text-gray-500 mt-0.5">
                                <i class="fas fa-clock mr-1"></i>
                                {{ $disposal->approved_at?->format('d/m/Y H:i') }} WIB
                            </p>
                        @elseif($disposal->status == 'rejected' && $disposal->rejection_reason)
                            <p class="text-xs text-gray-600 mt-1">
                                <strong>Alasan ditolak:</strong> {{ $disposal->rejection_reason }}
                            </p>
                            @if($disposal->rejected_at)
                                <p class="text-[11px] text-gray-500 mt-0.5">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $disposal->rejected_at->format('d/m/Y H:i') }} WIB
                                </p>
                            @endif
                        @elseif($disposal->status == 'pending')
                            <p class="text-xs text-gray-600 mt-1">
                                Menunggu tindakan dari Super Admin
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- AKSI (APPROVE & REJECT)                                       --}}
        {{-- ============================================================ --}}
        @if($disposal->isPending())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <p class="text-xs text-gray-500 uppercase font-semibold mb-3">
                <i class="fas fa-gavel mr-1 text-brand-500"></i>Tindakan
            </p>

            {{-- APPROVE --}}
            <form action="{{ route('super_admin.disposals.approve', $disposal) }}" method="POST"
                  onsubmit="return confirm('⚠️ Yakin mau setujui?\n\nBarang: {{ $disposal->item->name ?? '' }}\nKode Stok: {{ $disposal->stock_code ?? '' }}\n\nStok barang ini akan DIKURANGI dan kode stok akan DIHAPUS dari sistem.');"
                  class="mb-3">
                @csrf
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-4 py-3 rounded-xl transition-all font-semibold text-sm shadow-sm hover:shadow-md inline-flex items-center justify-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>Setujui & Hapus Kode Stok</span>
                </button>
            </form>

            {{-- REJECT --}}
            <form action="{{ route('super_admin.disposals.reject', $disposal) }}" method="POST"
                  onsubmit="return confirm('Yakin mau tolak pengajuan ini?');">
                @csrf

                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-700 mb-2">
                        Alasan Penolakan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="rejection_reason" 
                              rows="3" 
                              required
                              minlength="5"
                              maxlength="500"
                              class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100 transition resize-none"
                              placeholder="Contoh: Barang masih bisa diperbaiki, atau kode stok tidak sesuai...">{{ old('rejection_reason') }}</textarea>
                    @error('rejection_reason') 
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>{{ $message }}
                        </p> 
                    @enderror
                    <p class="text-[10px] text-gray-400 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Minimal 5 karakter
                    </p>
                </div>

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white px-4 py-3 rounded-xl transition-all font-semibold text-sm shadow-sm hover:shadow-md inline-flex items-center justify-center gap-2">
                    <i class="fas fa-times-circle"></i>
                    <span>Tolak Pengajuan</span>
                </button>
            </form>
        </div>
        @endif

    </div>
</div>
@endsection