@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/30">
                <i class="fas fa-coins text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Nilai Aset</h1>
                <p class="text-sm text-gray-500">Total kekayaan inventaris Yayasan Permata</p>
            </div>
        </div>
        <a href="{{ route('super_admin.dashboard') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition text-sm">
            <i class="fas fa-arrow-left"></i>Kembali
        </a>
    </div>

    {{-- KARTU RINGKASAN --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

        {{-- TOTAL NILAI ASET --}}
        <div class="bg-gradient-to-br from-amber-50 to-white rounded-2xl shadow-lg p-6 border border-amber-200">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-amber-600"></i>
                </div>
                <p class="text-sm font-medium text-gray-600">Total Nilai Aset</p>
            </div>
            <p class="text-3xl font-bold text-amber-600">
                Rp {{ number_format((float) $totalNilaiAset, 0, ',', '.') }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Harga × Stok semua barang</p>
        </div>

        {{-- TOTAL JUMLAH BARANG --}}
        <div class="bg-gradient-to-br from-teal-50 to-white rounded-2xl shadow-lg p-6 border border-teal-200">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-teal-100 flex items-center justify-center">
                    <i class="fas fa-boxes-stacked text-teal-600"></i>
                </div>
                <p class="text-sm font-medium text-gray-600">Total Jumlah Barang</p>
            </div>
            <p class="text-3xl font-bold text-teal-600">
                {{ number_format((int) $totalJumlahBarang, 0, ',', '.') }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Total unit (stok) semua barang</p>
        </div>

        {{-- TOTAL KATEGORI --}}
        <div class="bg-gradient-to-br from-blue-50 to-white rounded-2xl shadow-lg p-6 border border-blue-200">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-tags text-blue-600"></i>
                </div>
                <p class="text-sm font-medium text-gray-600">Total Kategori</p>
            </div>
            <p class="text-3xl font-bold text-blue-600">
                {{ count($perKategori) }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Kategori barang terdaftar</p>
        </div>

    </div>

    {{-- TABEL PER KATEGORI --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 mb-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-transparent">
            <h2 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-chart-pie text-amber-500"></i>
                Rincian Nilai Aset per Kategori
            </h2>
        </div>

        @if(empty($perKategori))
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-box-open text-5xl mb-3 text-gray-300"></i>
                <p>Belum ada data barang</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jumlah Unit</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Nilai Aset</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Persentase</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($perKategori as $i => $kat)
                        <tr class="hover:bg-amber-50/40 transition">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    <span class="font-medium text-gray-800">{{ $kat['nama'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-700">
                                {{ number_format((int) $kat['jumlah'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-amber-600">
                                Rp {{ number_format((float) $kat['nilai'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-20 bg-gray-200 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-amber-400 to-amber-600 h-2 rounded-full"
                                             style="width: {{ (float) $kat['persen'] }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-600 w-10 text-right">
                                        {{ $kat['persen'] }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gradient-to-r from-amber-100 to-amber-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-800 uppercase text-sm">
                                Total Keseluruhan
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-amber-700 text-lg">
                                Rp {{ number_format((float) $totalNilaiAset, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-gray-800">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

    {{-- DAFTAR BARANG DETAIL --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-teal-50 to-transparent">
            <h2 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-list text-teal-500"></i>
                Detail Semua Barang
            </h2>
        </div>

        @if($items->isEmpty())
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-box-open text-5xl mb-3 text-gray-300"></i>
                <p>Belum ada data barang</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stok</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($items as $item)
                        @php
                            // 🔥 CAST WAJIB — karena cast 'decimal:2' di model = STRING
                            $harga    = (float) ($item->price ?? 0);
                            $stok     = (int)   ($item->stock ?? 1);
                            $subtotal = $harga * $stok;
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-mono text-sm text-gray-600">{{ $item->code }}</td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $item->name }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">
                                    {{ optional($item->category)->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-700">
                                Rp {{ number_format($harga, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-700">
                                {{ $stok }}
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-800">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-right font-bold text-gray-800 uppercase text-sm">
                                Total Keseluruhan
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-amber-700 text-lg">
                                Rp {{ number_format((float) $totalNilaiAset, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection