@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Detail Barang</h1>
        <div class="flex gap-2 flex-wrap">
            <!-- 🔥 TOMBOL CETAK PDF -->
            <a href="{{ route('super_admin.items.pdf', $item) }}" 
               target="_blank"
               class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-file-pdf mr-2"></i>Cetak PDF
            </a>

            <!-- 🔥 TOMBOL EXPORT PNG (BARU, download bukan target=_blank) -->
            <a href="{{ route('super_admin.items.png', $item) }}" 
               download
               class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-file-image mr-2"></i>Export PNG
            </a>

            <a href="{{ route('super_admin.items.edit', $item) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('super_admin.items.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
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
                    <label class="text-sm text-gray-500">Tanggal Pembelian</label>
                    <p>{{ $item->purchase_date ? $item->purchase_date->format('d/m/Y') : '-' }}</p>
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
                    <label class="text-sm text-gray-500">Harga</label>
                    <p>{{ $item->price ? 'Rp ' . number_format($item->price, 0, ',', '.') : '-' }}</p>
                </div>

                <!-- 🔥 SUMBER DANA (MELALUI RELASI fundingSource) -->
                <div>
                    <label class="text-sm text-gray-500">Sumber Dana</label>
                    <p>
                        @if($item->fundingSource)
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $item->fundingSource->name == 'BOS' ? 'bg-blue-100 text-blue-700' : 
                                   ($item->fundingSource->name == 'APBY' ? 'bg-green-100 text-green-700' : 
                                   ($item->fundingSource->name == 'WAKAF' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-500')) }}">
                                {{ $item->fundingSource->name }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </p>
                </div>

                <div>
                    <label class="text-sm text-gray-500">Lokasi</label>
                    <p>{{ $item->location ?: '-' }}</p>
                </div>

                <div>
                    <label class="text-sm text-gray-500">Deskripsi</label>
                    <p>{{ $item->description ?: '-' }}</p>
                </div>

                <div>
                    <label class="text-sm text-gray-500">Dibuat</label>
                    <p>{{ $item->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Penyusutan Aset -->
    <div class="mt-6 bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow duration-300">
        <div class="flex items-center gap-2 mb-5">
            <div class="bg-teal-100 text-teal-600 p-2 rounded-lg">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3 class="font-semibold text-lg">Penyusutan Aset</h3>
        </div>

        @if($item->price && $item->purchase_date)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

                <!-- Nilai Buku -->
                <div class="group relative bg-green-50 border border-green-100 rounded-xl p-4 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-medium text-green-700 uppercase tracking-wide">Nilai Buku Saat Ini</span>
                        <i class="fas fa-wallet text-green-400 group-hover:scale-110 transition-transform"></i>
                    </div>
                    <p class="font-bold text-2xl text-green-600">
                        Rp {{ number_format($item->getBookValue(), 0, ',', '.') }}
                    </p>
                </div>

                <!-- Akumulasi Penyusutan -->
                <div class="group relative bg-red-50 border border-red-100 rounded-xl p-4 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-medium text-red-700 uppercase tracking-wide">Akumulasi Penyusutan</span>
                        <i class="fas fa-arrow-trend-down text-red-400 group-hover:scale-110 transition-transform"></i>
                    </div>
                    <p class="font-bold text-2xl text-red-500">
                        Rp {{ number_format($item->getAccumulatedDepreciation(), 0, ',', '.') }}
                    </p>
                </div>

                <!-- Penyusutan per Tahun -->
                <div class="group relative bg-blue-50 border border-blue-100 rounded-xl p-4 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-medium text-blue-700 uppercase tracking-wide">Penyusutan / Tahun</span>
                        <i class="fas fa-calendar text-blue-400 group-hover:scale-110 transition-transform"></i>
                    </div>
                    <p class="font-bold text-2xl text-blue-600">
                        Rp {{ number_format($item->getAnnualDepreciation(), 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="mb-2 flex justify-between items-center text-sm">
                    <span class="font-medium text-gray-600">Progres Penyusutan</span>
                    <span class="font-bold {{ $item->isFullyDepreciated() ? 'text-red-500' : 'text-teal-600' }}">
                        {{ $item->getDepreciationPercentage() }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div class="h-3 rounded-full transition-all duration-700 ease-out {{ $item->isFullyDepreciated() ? 'bg-gradient-to-r from-red-400 to-red-600' : 'bg-gradient-to-r from-teal-400 to-teal-600' }}"
                         style="width: {{ min($item->getDepreciationPercentage(), 100) }}%"></div>
                </div>

                <p class="text-xs text-gray-400 mt-3 flex items-center gap-1.5">
                    <i class="fas fa-circle-info"></i>
                    Masa manfaat kategori "{{ $item->category->name }}": {{ $item->getUsefulLifeYears() }} tahun.
                    @if($item->isFullyDepreciated())
                        <span class="text-red-500 font-medium ml-1">
                            <i class="fas fa-triangle-exclamation"></i> Barang ini sudah melewati masa manfaatnya.
                        </span>
                    @endif
                </p>
            </div>
        @else
            <div class="text-center py-6">
                <i class="fas fa-circle-info text-gray-300 text-3xl mb-2"></i>
                <p class="text-gray-400 text-sm">
                    Data penyusutan tidak dapat dihitung karena harga atau tanggal pembelian belum diisi.
                </p>
            </div>
        @endif
    </div>

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