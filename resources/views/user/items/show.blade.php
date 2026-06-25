@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">📦 Detail Barang</h1>
        <div>
            @if($item->status == 'available')
                <a href="{{ route('user.circulations.create') }}?item={{ $item->id }}" 
                   class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-hand-paper mr-2"></i>Ajukan Peminjaman
                </a>
            @endif
            <a href="{{ route('user.items.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition ml-2">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
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
            
            <!-- Status Barang -->
            <div class="mb-4 p-3 rounded-lg {{ $item->status == 'available' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                <div class="flex items-center justify-between">
                    <span class="font-medium">Status Barang</span>
                    @if($item->status == 'available')
                        <span class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-700">
                            🟢 Tersedia
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-sm bg-red-100 text-red-700">
                            🔴 Dipinjam
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="space-y-3">
                <div>
                    <label class="text-sm text-gray-500">Kode Barang</label>
                    <p class="font-mono font-bold text-lg">{{ $item->code }}</p>
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
                    <label class="text-sm text-gray-500">Kondisi</label>
                    <p>
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $item->condition == 'baik' ? 'bg-green-100 text-green-700' : 
                               ($item->condition == 'rusak' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst($item->condition) }}
                        </span>
                    </p>
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

    <!-- Info Peminjaman Aktif -->
    @if($item->status == 'borrowed' && $activeCirculation)
    <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-6">
        <h4 class="font-semibold text-red-800 mb-3">
            <i class="fas fa-info-circle mr-2"></i>Barang Sedang Dipinjam
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <span class="text-gray-600">Dipinjam oleh:</span>
                <p class="font-medium">{{ $activeCirculation->borrower_name }}</p>
            </div>
            <div>
                <span class="text-gray-600">Tanggal Pinjam:</span>
                <p class="font-medium">{{ $activeCirculation->borrow_date->format('d/m/Y') }}</p>
            </div>
            <div>
                <span class="text-gray-600">Tenggat Kembali:</span>
                <p class="font-medium {{ $activeCirculation->expected_return_date < now() ? 'text-red-600' : '' }}">
                    {{ $activeCirculation->expected_return_date->format('d/m/Y') }}
                    @if($activeCirculation->expected_return_date < now())
                        <span class="text-red-500 text-xs">⚠️ Terlambat!</span>
                    @endif
                </p>
            </div>
        </div>
        <p class="text-sm text-red-600 mt-3">
            <i class="fas fa-exclamation-triangle mr-1"></i>
            Barang ini sedang dipinjam dan tidak tersedia untuk dipinjam saat ini.
        </p>
    </div>
    @endif

    <!-- Riwayat Peminjaman -->
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