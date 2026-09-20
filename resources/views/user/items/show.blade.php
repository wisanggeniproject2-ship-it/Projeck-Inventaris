@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-4xl px-2 sm:px-4">
    <div class="flex flex-wrap justify-between items-center gap-3 mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Detail Barang</h1>
        <a href="{{ route('user.items.index') }}" class="text-gray-600 hover:text-gray-800 transition text-sm sm:text-base">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        <!-- Left - QR Code & Gambar -->
        <div class="bg-white rounded-xl shadow p-4 sm:p-6 hover:shadow-lg transition-shadow duration-300">
            <!-- QR Code -->
            <div class="text-center mb-4">
                <h3 class="font-semibold text-gray-700 text-sm mb-2">QR Code</h3>
                @if($item->qr_code_url)
                <img src="{{ $item->qr_code_url }}" alt="QR Code" class="mx-auto w-32 sm:w-40">
                @else
                <div class="bg-gray-100 p-4 rounded-lg">
                    <i class="fas fa-qrcode text-5xl text-gray-400"></i>
                    <p class="text-gray-500 text-xs mt-1">QR Code belum tersedia</p>
                </div>
                @endif
            </div>
            
            <!-- GAMBAR BARANG -->
            <div class="mt-4">
                <h3 class="font-semibold text-gray-700 text-sm mb-2">Gambar Barang</h3>
                <img src="{{ $item->image_url ?? asset('images/no-image.png') }}" alt="{{ $item->name }}" 
                     class="w-full h-40 sm:h-48 object-cover rounded-lg border border-gray-200 hover:opacity-90 transition-opacity">
            </div>
        </div>

        <!-- Right - Detail -->
        <div class="bg-white rounded-xl shadow p-4 sm:p-6 hover:shadow-lg transition-shadow duration-300">
            <h3 class="font-semibold text-gray-700 text-sm mb-4 border-b pb-2">Informasi Barang</h3>
            
            <div class="space-y-2 text-sm">
                <div class="flex justify-between py-1.5 border-b border-gray-100 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Kode Barang</span>
                    <span class="font-mono font-medium">{{ $item->code }}</span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Nama Barang</span>
                    <span class="font-medium">{{ $item->name }}</span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Kategori</span>
                    <span>{{ $item->category->name }}</span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Unit</span>
                    <span>{{ $item->unit->name }}</span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Tanggal Beli</span>
                    <span>{{ $item->purchase_date ? $item->purchase_date->format('d/m/Y') : '-' }}</span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Kondisi</span>
                    <span>
                        <span class="px-2 py-0.5 text-xs rounded-full
                            {{ $item->condition == 'baik' ? 'bg-green-100 text-green-700' : 
                               ($item->condition == 'rusak' ? 'bg-yellow-100 text-yellow-700' : 'bg-orange-100 text-orange-700') }}">
                            {{ ucfirst($item->condition) }}
                        </span>
                    </span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Status</span>
                    <span>
                        <span class="px-2 py-0.5 text-xs rounded-full
                            {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : 
                               ($item->status == 'borrowed' ? 'bg-red-100 text-red-700' : 
                               ($item->status == 'disposed' ? 'bg-gray-200 text-gray-600' : 'bg-yellow-100 text-yellow-700')) }}">
                            {{ $item->status == 'available' ? 'Tersedia' : 
                               ($item->status == 'borrowed' ? 'Dipinjam' : 
                               ($item->status == 'disposed' ? 'Dihapus dari Aset' : 'Perbaikan')) }}
                        </span>
                    </span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Harga</span>
                    <span>{{ $item->price ? 'Rp ' . number_format($item->price, 0, ',', '.') : '-' }}</span>
                </div>

                <!-- SUMBER DANA -->
                <div class="flex justify-between py-1.5 border-b border-gray-100 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Sumber Dana</span>
                    <span>
                        @if($item->fundingSource)
                            <span class="px-2 py-0.5 text-xs rounded-full
                                {{ $item->fundingSource->name == 'BOS' ? 'bg-blue-100 text-blue-700' : 
                                   ($item->fundingSource->name == 'APBY' ? 'bg-green-100 text-green-700' : 
                                   ($item->fundingSource->name == 'WAKAF' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-500')) }}">
                                {{ $item->fundingSource->name }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-gray-100 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Lokasi</span>
                    <span>{{ $item->location ?: '-' }}</span>
                </div>

                <div class="flex justify-between py-1.5 hover:bg-gray-50 rounded px-1 -mx-1 transition-colors">
                    <span class="text-gray-500">Deskripsi</span>
                    <span class="text-right max-w-[60%]">{{ $item->description ?: '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== AJUKAN PENGHAPUSAN ASET ==================== -->
    <div class="mt-4 sm:mt-6 bg-white rounded-xl shadow p-4 sm:p-6 hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center gap-2 mb-3">
            <div class="bg-red-100 text-red-600 p-2 rounded-lg shrink-0">
                <i class="fas fa-trash-can"></i>
            </div>
            <h3 class="font-semibold text-gray-800 text-sm sm:text-base">Pengajuan Penghapusan Aset</h3>
        </div>

        @if($item->status === 'disposed')
            <div class="flex items-start gap-2 bg-gray-50 border border-gray-200 rounded-lg p-3 text-sm text-gray-600">
                <i class="fas fa-circle-info mt-0.5"></i>
                <p>Barang ini sudah dihapus dari daftar aset.</p>
            </div>
        @elseif($item->hasPendingDisposalRequest())
            <div class="flex items-start gap-2 bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm text-yellow-700">
                <i class="fas fa-clock mt-0.5"></i>
                <p>Ada pengajuan penghapusan untuk barang ini yang masih menunggu konfirmasi Super Admin.</p>
            </div>
        @else
            <p class="text-sm text-gray-500 mb-4">
                Barang ini rusak parah dan tidak bisa dipakai lagi? Ajukan penghapusan dari daftar aset.
            </p>
            <button type="button" onclick="document.getElementById('disposalModal').classList.remove('hidden')"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-red-500 hover:bg-red-600 hover:shadow-md active:scale-[0.98] text-white px-4 py-2.5 rounded-lg transition-all text-sm font-medium">
                <i class="fas fa-paper-plane"></i>
                Ajukan Penghapusan
            </button>
        @endif
    </div>
</div>

<!-- ==================== MODAL PENGAJUAN ==================== -->
<div id="disposalModal" class="hidden fixed inset-0 z-[999] flex items-end sm:items-center justify-center p-0 sm:p-4">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('disposalModal').classList.add('hidden')"></div>

    <!-- Modal Content -->
    <div class="relative bg-white w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl shadow-2xl p-5 sm:p-6 animate-fadeInUp max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-lg text-gray-800">Ajukan Penghapusan</h3>
            <button type="button" onclick="document.getElementById('disposalModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition p-1">
                <i class="fas fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="bg-gray-50 rounded-lg p-3 mb-4">
            <p class="text-xs text-gray-500">Barang</p>
            <p class="font-semibold text-gray-800">{{ $item->name }}</p>
            <p class="text-xs text-gray-400 font-mono">{{ $item->code }}</p>
        </div>

        <form action="{{ route('user.disposals.store') }}" method="POST">
            @csrf
            <input type="hidden" name="item_id" value="{{ $item->id }}">

            <label class="block text-sm font-medium mb-2 text-gray-700">Alasan Penghapusan *</label>
            <textarea name="reason" rows="4" required minlength="10"
                      class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-400 transition"
                      placeholder="Jelaskan kondisi barang, misal: layar TV pecah dan tidak bisa menyala sama sekali."></textarea>
            <p class="text-xs text-gray-400 mt-1">Minimal 10 karakter.</p>

            <div class="flex gap-2 mt-5">
                <button type="button" onclick="document.getElementById('disposalModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2.5 border rounded-lg hover:bg-gray-50 transition text-sm font-medium text-gray-600">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 bg-red-500 hover:bg-red-600 hover:shadow-md active:scale-[0.98] text-white px-4 py-2.5 rounded-lg transition-all text-sm font-medium">
                    <i class="fas fa-paper-plane mr-1"></i>Kirim
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(24px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeInUp {
        animation: fadeInUp 0.25s ease-out;
    }
</style>
@endpush
@endsection