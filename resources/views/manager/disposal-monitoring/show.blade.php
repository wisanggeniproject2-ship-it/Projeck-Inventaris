@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-trash-can text-red-600 mr-2"></i>Detail Penghapusan Aset
            </h1>
            <p class="text-sm text-gray-500 mt-1">Monitoring aktivitas penghapusan aset</p>
        </div>
        <a href="{{ route('manager.disposal-monitoring.index') }}"
           class="text-gray-600 hover:text-gray-800 inline-flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>Kembali
        </a>
    </div>

    @php
        $statusConfig = [
            'pending'  => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200', 'icon' => 'fa-clock', 'label' => 'Menunggu Konfirmasi'],
            'approved' => ['bg' => 'bg-red-50',    'text' => 'text-red-700',    'border' => 'border-red-200',    'icon' => 'fa-check-circle', 'label' => 'Disetujui (Barang Dihancurkan)'],
            'rejected' => ['bg' => 'bg-gray-50',   'text' => 'text-gray-600',   'border' => 'border-gray-200',   'icon' => 'fa-times-circle', 'label' => 'Ditolak'],
        ];
        $sc = $statusConfig[$disposal->status] ?? $statusConfig['pending'];
        $photos = is_array($disposal->photos) ? $disposal->photos : [];
    @endphp

    {{-- STATUS BANNER --}}
    <div class="{{ $sc['bg'] }} {{ $sc['border'] }} border-2 rounded-2xl p-5 mb-6 flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-white shadow-sm flex items-center justify-center shrink-0">
            <i class="fas {{ $sc['icon'] }} {{ $sc['text'] }} text-2xl"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs uppercase font-bold tracking-wide {{ $sc['text'] }}">Status Pengajuan</p>
            <p class="text-xl font-bold {{ $sc['text'] }} mt-0.5">{{ $sc['label'] }}</p>
            @if($disposal->status == 'approved' && $disposal->approved_at)
            <p class="text-xs {{ $sc['text'] }} mt-1 opacity-80">
                <i class="fas fa-check text-[10px] mr-1"></i>
                Disetujui oleh <strong>{{ $disposal->approver->name ?? '-' }}</strong>
                pada {{ $disposal->approved_at->translatedFormat('d F Y H:i') }} WIB
            </p>
            @elseif($disposal->status == 'rejected' && $disposal->rejected_at)
            <p class="text-xs {{ $sc['text'] }} mt-1 opacity-80">
                <i class="fas fa-times text-[10px] mr-1"></i>
                Ditolak pada {{ $disposal->rejected_at->translatedFormat('d F Y H:i') }} WIB
            </p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KOLOM KIRI (2/3) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- INFO BARANG --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center">
                        <i class="fas fa-box text-brand-600 text-sm"></i>
                    </span>
                    Informasi Barang
                </h3>

                <div class="flex gap-4 mb-4">
                    @if($disposal->item && $disposal->item->image)
                        <img src="{{ $disposal->item->image_url }}" alt="{{ $disposal->item->name }}"
                             class="w-24 h-24 rounded-xl object-cover border border-gray-200 shrink-0">
                    @else
                        <div class="w-24 h-24 rounded-xl bg-gray-100 flex items-center justify-center border border-gray-200 shrink-0">
                            <i class="fas fa-box text-gray-300 text-3xl"></i>
                        </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-lg text-gray-800">{{ $disposal->item->name ?? '-' }}</p>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @if($disposal->item && $disposal->item->category)
                            <span class="inline-flex items-center gap-1 text-[10px] font-medium bg-blue-50 text-blue-700 px-2 py-1 rounded-lg border border-blue-100">
                                <i class="fas fa-tag text-[8px]"></i>{{ $disposal->item->category->name }}
                            </span>
                            @endif
                            @if($disposal->item && $disposal->item->unit)
                            <span class="inline-flex items-center gap-1 text-[10px] font-medium bg-teal-50 text-teal-700 px-2 py-1 rounded-lg border border-teal-100">
                                <i class="fas fa-building text-[8px]"></i>{{ $disposal->item->unit->name }}
                            </span>
                            @endif
                        </div>
                        @if($disposal->item && ($disposal->item->full_code ?? $disposal->item->code))
                        <div class="inline-flex items-start gap-1 mt-2 px-2 py-1 rounded-md border"
                             style="background: #0F6B5F10; border-color: #0F6B5F30;">
                            <i class="fas fa-qrcode text-[9px] mt-0.5 shrink-0" style="color: #0F6B5F;"></i>
                            <span class="font-mono text-[10px] font-semibold break-all" style="color: #0F6B5F;">
                                {{ $disposal->item->full_code ?? $disposal->item->code }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Kode stok yang dihapus --}}
                @if($disposal->stock_code)
                <div class="p-3 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Kode Stok yang Diajukan Hapus</p>
                    <div class="inline-flex items-start gap-1 px-2 py-1 rounded-md text-[11px] font-mono font-bold break-all"
                         style="background: #0F6B5F15; color: #0F6B5F;">
                        <i class="fas fa-barcode text-[9px] mt-0.5 shrink-0"></i>
                        {{ $disposal->stock_code }}
                    </div>
                </div>
                @endif
            </div>

            {{-- ALASAN PENGAJUAN --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                        <i class="fas fa-comment-alt text-amber-600 text-sm"></i>
                    </span>
                    Alasan Penghapusan
                </h3>
                <div class="bg-amber-50 border-l-4 border-amber-400 rounded-r-xl p-4">
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $disposal->reason ?? '-' }}</p>
                </div>
            </div>

            {{-- 🔥 FOTO BUKTI --}}
            @if(count($photos) > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center">
                        <i class="fas fa-camera text-purple-600 text-sm"></i>
                    </span>
                    Foto Bukti Kerusakan
                    <span class="ml-1 px-2 py-0.5 text-[10px] font-bold rounded-full bg-purple-500 text-white">
                        {{ count($photos) }}
                    </span>
                </h3>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($photos as $index => $photo)
                    <div class="group relative rounded-xl overflow-hidden border-2 border-gray-200 hover:border-purple-400 transition cursor-pointer"
                         onclick="openPhotoModal('{{ asset('storage/' . $photo) }}', {{ $index + 1 }})">
                        <img src="{{ asset('storage/' . $photo) }}" alt="Foto Bukti {{ $index + 1 }}"
                             class="w-full h-32 object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="absolute bottom-2 left-2 text-[10px] text-white font-bold opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fas fa-search-plus mr-1"></i>Klik untuk perbesar
                        </div>
                        <div class="absolute top-2 right-2 bg-black/50 backdrop-blur text-white text-[10px] font-bold px-1.5 py-0.5 rounded">
                            {{ $index + 1 }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- CATATAN ADMIN (kalau rejected) --}}
            @if($disposal->status == 'rejected' && $disposal->rejection_reason)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600 text-sm"></i>
                    </span>
                    Alasan Ditolak
                </h3>
                <div class="bg-red-50 border-l-4 border-red-400 rounded-r-xl p-4">
                    <p class="text-sm text-red-700 leading-relaxed">{{ $disposal->rejection_reason }}</p>
                </div>
            </div>
            @endif

        </div>

        {{-- KOLOM KANAN (1/3) --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- PENGAJU --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-sm">
                    <i class="fas fa-user text-blue-500"></i>
                    Diajukan Oleh
                </h3>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white text-lg font-bold shrink-0">
                        {{ strtoupper(substr($disposal->user->name ?? '?', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 truncate">{{ $disposal->user->name ?? '-' }}</p>
                        <p class="text-[11px] text-gray-500">{{ $disposal->user->email ?? '' }}</p>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100 text-xs text-gray-500">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    {{ $disposal->created_at->translatedFormat('d F Y') }}
                    &bull;
                    <i class="fas fa-clock mx-1"></i>
                    {{ $disposal->created_at->format('H:i') }} WIB
                </div>
            </div>

            {{-- APPROVER (kalau sudah diproses) --}}
            @if($disposal->approver && $disposal->status != 'pending')
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-sm">
                    <i class="fas fa-user-shield text-emerald-500"></i>
                    {{ $disposal->status == 'approved' ? 'Disetujui Oleh' : 'Ditolak Oleh' }}
                </h3>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white text-lg font-bold shrink-0">
                        {{ strtoupper(substr($disposal->approver->name ?? '?', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 truncate">{{ $disposal->approver->name }}</p>
                        <p class="text-[11px] text-gray-500">{{ $disposal->approver->email ?? '' }}</p>
                    </div>
                </div>
                @if($disposal->approved_at)
                <div class="mt-3 pt-3 border-t border-gray-100 text-xs text-gray-500">
                    <i class="fas fa-check mr-1"></i>
                    {{ $disposal->approved_at->translatedFormat('d F Y') }}
                    &bull;
                    <i class="fas fa-clock mx-1"></i>
                    {{ $disposal->approved_at->format('H:i') }} WIB
                </div>
                @endif
            </div>
            @endif

            {{-- INFO UNIT --}}
            @if($disposal->item && $disposal->item->unit)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-sm">
                    <i class="fas fa-building text-teal-500"></i>
                    Unit Barang
                </h3>
                <div class="inline-flex items-center gap-2 px-3 py-2 bg-teal-50 border border-teal-100 rounded-xl">
                    <i class="fas fa-building text-teal-600"></i>
                    <span class="font-semibold text-teal-700">{{ $disposal->item->unit->name }}</span>
                </div>
            </div>
            @endif

            {{-- INFO SISTEM --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-sm">
                    <i class="fas fa-info-circle text-gray-500"></i>
                    Info Sistem
                </h3>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-500">ID Pengajuan</span>
                        <span class="font-semibold text-gray-700">#{{ $disposal->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Item ID</span>
                        <span class="font-semibold text-gray-700">#{{ $disposal->item_id }}</span>
                    </div>
                    @if($disposal->item_stock_id)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Stock ID</span>
                        <span class="font-semibold text-gray-700">#{{ $disposal->item_stock_id }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- NOTIF MONITORING --}}
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 text-center">
                <i class="fas fa-eye text-blue-500 text-lg mb-1"></i>
                <p class="text-xs text-blue-700 font-medium">Mode Monitoring</p>
                <p class="text-[10px] text-blue-600 mt-1">Anda hanya bisa melihat, tidak bisa mengubah status</p>
            </div>

        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL PREVIEW FOTO                                            --}}
{{-- ============================================================ --}}
<div id="photoModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/90 backdrop-blur-sm" onclick="closePhotoModal()"></div>

    <div class="relative max-w-4xl w-full" style="animation: modalIn 0.25s ease-out;">
        <button type="button" onclick="closePhotoModal()"
                class="absolute -top-12 right-0 text-white/80 hover:text-white transition w-10 h-10 rounded-lg hover:bg-white/10 flex items-center justify-center">
            <i class="fas fa-times text-2xl"></i>
        </button>

        <img id="photoModalImg" src="" alt="Preview" class="w-full max-h-[85vh] object-contain rounded-xl shadow-2xl">

        <p class="text-center text-white/70 text-xs mt-3">
            <i class="fas fa-image mr-1"></i>
            Foto <span id="photoModalIndex">1</span>
        </p>
    </div>
</div>

<style>
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95); }
        to   { opacity: 1; transform: scale(1); }
    }
</style>

@push('scripts')
<script>
    function openPhotoModal(src, index) {
        const modal = document.getElementById('photoModal');
        const img = document.getElementById('photoModalImg');
        const idx = document.getElementById('photoModalIndex');

        img.src = src;
        idx.textContent = index;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closePhotoModal() {
        const modal = document.getElementById('photoModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closePhotoModal();
    });
</script>
@endpush
@endsection