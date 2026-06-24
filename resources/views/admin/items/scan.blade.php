@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-2xl">
    <div class="bg-white rounded-lg shadow p-8">
        <div class="text-center mb-6">
            <i class="fas fa-check-circle text-green-500 text-5xl"></i>
            <h1 class="text-2xl font-bold mt-4">Detail Barang</h1>
            <p class="text-gray-500">Hasil Scan QR Code</p>
        </div>

        <div class="border-t pt-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-500">Kode Barang</label>
                    <p class="font-mono font-bold text-lg">{{ $item->code }}</p>
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
                <div class="col-span-2">
                    <label class="text-sm text-gray-500">Nama Barang</label>
                    <p class="font-medium text-xl">{{ $item->name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Unit</label>
                    <p>{{ $item->unit->name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Kondisi</label>
                    <p>{{ ucfirst($item->condition) }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Tanggal Pembelian</label>
                    <p>{{ $item->purchase_date ? $item->purchase_date->format('d/m/Y') : '-' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Lokasi</label>
                    <p>{{ $item->location ?: '-' }}</p>
                </div>
                <div class="col-span-2">
                    <label class="text-sm text-gray-500">Deskripsi</label>
                    <p>{{ $item->description ?: '-' }}</p>
                </div>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t flex justify-center gap-4">
            <a href="{{ route('landing') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">
                <i class="fas fa-home mr-2"></i>Kembali
            </a>
            @auth
                @if(auth()->user()->role == 'user')
                    <a href="{{ route('user.circulations.create', ['item' => $item->id]) }}" 
                       class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600">
                        <i class="fas fa-hand-paper mr-2"></i>Ajukan Peminjaman
                    </a>
                @endif
            @endauth
        </div>
    </div>
</div>
@endsection