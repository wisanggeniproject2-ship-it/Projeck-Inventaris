@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Detail Barang</h1>
        <a href="{{ route('manager.items.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
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

    <!-- Circulation History -->
    <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-lg mb-4">Riwayat Peminjaman</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Peminjam</th>
                        <th class="px-4 py-2 text-left">Tanggal Pinjam</th>
                        <th class="px-4 py-2 text-left">Tenggat</th>
                        <th class="px-4 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($item->circulations as $circulation)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $circulation->borrower_name }}</td>
                        <td class="px-4 py-2">{{ $circulation->borrow_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">{{ $circulation->expected_return_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $circulation->status == 'approved' ? 'bg-green-100 text-green-700' : 
                                   ($circulation->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                   ($circulation->status == 'returned' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                                {{ ucfirst($circulation->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                            Belum ada riwayat peminjaman
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection