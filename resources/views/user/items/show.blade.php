@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Detail Barang</h1>
        <div>
            @if($item->status == 'available')
            <a href="{{ route('user.circulations.create') }}?item={{ $item->id }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                <i class="fas fa-hand-paper mr-2"></i>Ajukan Peminjaman
            </a>
            @endif
            <a href="{{ route('user.items.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 ml-2">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Left Column - Image & QR -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center mb-4">
                <h3 class="font-semibold text-lg mb-2">QR Code</h3>
                @if($item->qr_code_url)
                <img src="{{ $item->qr_code_url }}" alt="QR Code" class="mx-auto w-48">
                @else
                <p class="text-gray-500">QR Code belum tersedia</p>
                @endif
            </div>
            <div class="mt-4">
                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-64 object-cover rounded-lg">
            </div>
        </div>

        <!-- Right Column - Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="mb-4">
                <span class="px-3 py-1 rounded-full text-sm 
                    {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $item->status == 'available' ? 'Tersedia' : 'Dipinjam' }}
                </span>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="text-sm text-gray-500">Kode Barang</label>
                    <p class="font-medium">{{ $item->code }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Nama Barang</label>
                    <p class="font-medium text-lg">{{ $item->name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Kategori</label>
                    <p>{{ $item->category->name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Unit</label>
                    <p>{{ $item->unit->name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Lokasi</label>
                    <p><i class="fas fa-map-marker-alt mr-2"></i>{{ $item->location }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Sumber Anggaran</label>
                    <p>{{ $item->budget_source ?: '-' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Deskripsi</label>
                    <p>{{ $item->description ?: '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Peminjaman Aktif -->
    @php
        $activeCirculation = $item->activeCirculation;
    @endphp
    
    @if($activeCirculation && $activeCirculation->status == 'approved')
    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-yellow-600 mt-1 mr-3"></i>
            <div>
                <h4 class="font-semibold text-yellow-800">Sedang Dipinjam</h4>
                <p class="text-sm text-yellow-700">
                    Dipinjam oleh: {{ $activeCirculation->borrower_name }}<br>
                    Tanggal pinjam: {{ $activeCirculation->borrow_date->format('d/m/Y') }}<br>
                    Tenggat kembali: {{ $activeCirculation->expected_return_date->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection