@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Detail Barang</h1>
        <a href="{{ route('user.items.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Left - QR Code & Gambar -->
        <div class="bg-white rounded-lg shadow p-6">
            <!-- QR Code -->
            <div class="text-center mb-4">
                <h3 class="font-semibold text-gray-700 text-sm mb-2">QR Code</h3>
                @if($item->qr_code_url)
                <img src="{{ $item->qr_code_url }}" alt="QR Code" class="mx-auto w-40">
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
                     class="w-full h-48 object-cover rounded-lg border border-gray-200">
            </div>
        </div>

        <!-- Right - Detail -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold text-gray-700 text-sm mb-4 border-b pb-2">Informasi Barang</h3>
            
            <div class="space-y-2 text-sm">
                <div class="flex justify-between py-1 border-b border-gray-100">
                    <span class="text-gray-500">Kode Barang</span>
                    <span class="font-mono font-medium">{{ $item->code }}</span>
                </div>

                <div class="flex justify-between py-1 border-b border-gray-100">
                    <span class="text-gray-500">Nama Barang</span>
                    <span class="font-medium">{{ $item->name }}</span>
                </div>

                <div class="flex justify-between py-1 border-b border-gray-100">
                    <span class="text-gray-500">Kategori</span>
                    <span>{{ $item->category->name }}</span>
                </div>

                <div class="flex justify-between py-1 border-b border-gray-100">
                    <span class="text-gray-500">Unit</span>
                    <span>{{ $item->unit->name }}</span>
                </div>

                <div class="flex justify-between py-1 border-b border-gray-100">
                    <span class="text-gray-500">Tanggal Beli</span>
                    <span>{{ $item->purchase_date ? $item->purchase_date->format('d/m/Y') : '-' }}</span>
                </div>

                <div class="flex justify-between py-1 border-b border-gray-100">
                    <span class="text-gray-500">Kondisi</span>
                    <span>
                        <span class="px-2 py-0.5 text-xs rounded-full
                            {{ $item->condition == 'baik' ? 'bg-green-100 text-green-700' : 
                               ($item->condition == 'rusak' ? 'bg-yellow-100 text-yellow-700' : 'bg-orange-100 text-orange-700') }}">
                            {{ ucfirst($item->condition) }}
                        </span>
                    </span>
                </div>

                <div class="flex justify-between py-1 border-b border-gray-100">
                    <span class="text-gray-500">Status</span>
                    <span>
                        <span class="px-2 py-0.5 text-xs rounded-full
                            {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : 
                               ($item->status == 'borrowed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ $item->status == 'available' ? 'Tersedia' : ($item->status == 'borrowed' ? 'Dipinjam' : 'Perbaikan') }}
                        </span>
                    </span>
                </div>

                <div class="flex justify-between py-1 border-b border-gray-100">
                    <span class="text-gray-500">Harga</span>
                    <span>{{ $item->price ? 'Rp ' . number_format($item->price, 0, ',', '.') : '-' }}</span>
                </div>

                <!-- SUMBER DANA -->
                <div class="flex justify-between py-1 border-b border-gray-100">
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

                <div class="flex justify-between py-1 border-b border-gray-100">
                    <span class="text-gray-500">Lokasi</span>
                    <span>{{ $item->location ?: '-' }}</span>
                </div>

                <div class="flex justify-between py-1">
                    <span class="text-gray-500">Deskripsi</span>
                    <span class="text-right max-w-[60%]">{{ $item->description ?: '-' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection