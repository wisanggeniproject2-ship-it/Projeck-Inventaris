@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Daftar Barang - {{ auth()->user()->unit->name }}</h1>
        <a href="{{ route('admin_unit.items.create') }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition shadow-sm">
            <i class="fas fa-plus mr-2"></i>Tambah Barang
        </a>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari kode, nama, atau lokasi..." 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="category" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
                @if(request('search') || request('category'))
                <a href="{{ route('admin_unit.items.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition">
                    <i class="fas fa-times mr-2"></i>Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table dengan Gambar -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gambar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kondisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sumber Dana</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($items as $item)
                    @php
                        $stock = $item->stock ?? 0;
                        if ($stock == 0) {
                            $stockTextColor = 'text-red-600';
                            $stockColor = 'bg-red-100 text-red-700 border-red-200';
                            $stockIcon = 'fa-times-circle';
                            $stockLabel = 'Habis';
                        } elseif ($stock <= 3) {
                            $stockTextColor = 'text-yellow-600';
                            $stockColor = 'bg-yellow-100 text-yellow-700 border-yellow-200';
                            $stockIcon = 'fa-exclamation-triangle';
                            $stockLabel = 'Menipis';
                        } else {
                            $stockTextColor = 'text-green-600';
                            $stockColor = 'bg-green-100 text-green-700 border-green-200';
                            $stockIcon = 'fa-check-circle';
                            $stockLabel = 'Tersedia';
                        }
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <img src="{{ $item->image_url ?? asset('images/no-image.png') }}" alt="{{ $item->name }}" 
                                 class="w-12 h-12 rounded-lg object-cover border border-gray-200">
                        </td>

                        {{-- 🔥 KOLOM KODE — sekarang cuma tampil kode lengkap --}}
                        <td class="px-6 py-4">
                            <div class="inline-flex items-start gap-1 px-2 py-1 rounded-md border"
                                 style="background: #0F6B5F10; border-color: #0F6B5F30;">
                                <i class="fas fa-qrcode text-[9px] mt-0.5" style="color: #0F6B5F;"></i>
                                <span class="font-mono text-[10px] font-semibold leading-tight break-all"
                                      style="color: #0F6B5F;">
                                    {{ $item->full_code ?? $item->code }}
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-4 font-medium">{{ $item->name }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">
                                {{ $item->category->name }}
                            </span>
                        </td>
                        <!-- 🔥 KOLOM STOK -->
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center gap-1">
                                <span class="text-2xl font-bold {{ $stockTextColor }}">
                                    {{ $stock }}
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-medium rounded-full border {{ $stockColor }}">
                                    <i class="fas {{ $stockIcon }} text-[9px]"></i>
                                    {{ $stockLabel }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $item->condition == 'baik' ? 'bg-green-100 text-green-700' : 
                                   ($item->condition == 'rusak' ? 'bg-yellow-100 text-yellow-700' : 'bg-orange-100 text-orange-700') }}">
                                {{ ucfirst($item->condition) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : 
                                   ($item->status == 'borrowed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ $item->status == 'available' ? 'Tersedia' : ($item->status == 'borrowed' ? 'Dipinjam' : 'Perbaikan') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            {{ $item->fundingSource->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <!-- Detail -->
                                <a href="{{ route('admin_unit.items.show', $item) }}" 
                                   class="text-blue-600 hover:text-blue-800" 
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <!-- 🔥 CETAK PDF -->
                                <a href="{{ route('admin_unit.items.pdf', $item) }}" 
                                   target="_blank"
                                   class="text-red-600 hover:text-red-800" 
                                   title="Cetak PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>

                                <!-- 🔥 EXPORT PNG -->
                                <a href="{{ route('admin_unit.items.png', $item) }}" 
                                   download
                                   class="text-teal-600 hover:text-teal-800" 
                                   title="Export PNG">
                                    <i class="fas fa-file-image"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-box-open text-4xl mb-2 block"></i>
                            Belum ada data barang di unit ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $items->withQueryString()->links() }}
    </div>
</div>
@endsection