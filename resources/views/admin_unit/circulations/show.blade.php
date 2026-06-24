@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-3xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Detail Peminjaman</h1>
        <a href="{{ route('admin_unit.circulations.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="border-b px-6 py-4">
            <div class="flex justify-between items-center">
                <h3 class="font-semibold text-lg">Informasi Peminjaman</h3>
                <span class="px-3 py-1 rounded-full text-sm
                    {{ $circulation->status == 'approved' ? 'bg-green-100 text-green-700' : 
                       ($circulation->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                       ($circulation->status == 'returned' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                    {{ ucfirst($circulation->status) }}
                </span>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-500">Barang</label>
                    <p class="font-medium">{{ $circulation->item->name }}</p>
                    <p class="text-sm text-gray-600">Kode: {{ $circulation->item->code }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Nama Peminjam</label>
                    <p class="font-medium">{{ $circulation->borrower_name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Tanggal Pinjam</label>
                    <p>{{ $circulation->borrow_date->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Tenggat Pengembalian</label>
                    <p>{{ $circulation->expected_return_date->format('d/m/Y') }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Tanggal Kembali</label>
                    <p>{{ $circulation->return_date ? $circulation->return_date->format('d/m/Y') : '-' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">User Pengaju</label>
                    <p>{{ $circulation->user->name }}</p>
                    <p class="text-sm text-gray-600">{{ $circulation->user->email }}</p>
                </div>
                <div class="col-span-2">
                    <label class="text-sm text-gray-500">Tujuan Peminjaman</label>
                    <p class="mt-1">{{ $circulation->purpose ?: '-' }}</p>
                </div>
                @if($circulation->approved_by)
                <div>
                    <label class="text-sm text-gray-500">Disetujui Oleh</label>
                    <p>{{ $circulation->approver->name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Waktu Persetujuan</label>
                    <p>{{ $circulation->approved_at ? $circulation->approved_at->format('d/m/Y H:i') : '-' }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection