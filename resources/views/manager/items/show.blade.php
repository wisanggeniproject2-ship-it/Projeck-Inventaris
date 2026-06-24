@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Detail Barang</h1>
        <a href="{{ route('manager.items.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Left - QR Code -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold text-lg mb-4 text-center">QR Code</h3>
            <div class="text-center">
                @if($item->qr_code_url)
                <img src="{{ $item->qr_code_url }}" alt="QR Code" class="mx-auto w-64">
                <p class="text-xs text-gray-500 mt-2">Scan QR untuk melihat detail</p>
                @else
                <div class="bg-gray-100 p-6 rounded-lg">
                    <i class="fas fa-qrcode text-6xl text-gray-400"></i>
                    <p class="text-gray-500 mt-2">QR Code belum tersedia</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Right - Detail -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold text-lg mb-4">Informasi Barang</h3>
            
            <div class="space-y-3">
                <div>
                    <label class="text-sm text-gray-500">Kode Barang</label>
                    <p class="font-mono font-bold">{{ $item->code }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Nama Barang</label>
                    <p class="font-medium text-lg">{{ $item->name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Unit</label>
                    <p>{{ $item->unit->name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Kategori</label>
                    <p>{{ $item->category->name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Kondisi</label>
                    <p>{{ ucfirst($item->condition) }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Status</label>
                    <p>
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : 
                               ($item->status == 'borrowed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ $item->status == 'available' ? 'Tersedia' : ($item->status == 'borrowed' ? 'Dipinjam' : 'Perbaikan') }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Lokasi</label>
                    <p>{{ $item->location ?: '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection