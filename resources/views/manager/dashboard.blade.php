@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-6">Dashboard Manager</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Barang</p>
                    <p class="text-2xl font-bold">{{ $stats['total_items'] }}</p>
                </div>
                <i class="fas fa-boxes text-3xl text-blue-500"></i>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Dipinjam</p>
                    <p class="text-2xl font-bold">{{ $stats['total_borrowed'] }}</p>
                </div>
                <i class="fas fa-hand-paper text-3xl text-yellow-500"></i>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Pending</p>
                    <p class="text-2xl font-bold">{{ $stats['total_pending'] }}</p>
                </div>
                <i class="fas fa-clock text-3xl text-orange-500"></i>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Unit</p>
                    <p class="text-2xl font-bold">{{ $stats['total_units'] }}</p>
                </div>
                <i class="fas fa-building text-3xl text-green-500"></i>
            </div>
        </div>
    </div>

    <!-- Barang per Unit -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-lg">Statistik Barang per Unit</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($itemsByUnit as $unit)
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-gray-600">{{ $unit->name }}</p>
                    <p class="text-2xl font-bold">{{ $unit->items_count }}</p>
                    <p class="text-xs text-gray-500">Barang</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Items -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="font-semibold text-lg">Barang Terbaru</h3>
            <a href="{{ route('manager.items.index') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                Lihat Semua →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentItems as $item)
                    <tr class="border-t">
                        <td class="px-6 py-4">{{ $item->code }}</td>
                        <td class="px-6 py-4">{{ $item->name }}</td>
                        <td class="px-6 py-4">{{ $item->unit->name }}</td>
                        <td class="px-6 py-4">{{ $item->location }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : 
                                   ($item->status == 'borrowed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ $item->status == 'available' ? 'Tersedia' : ($item->status == 'borrowed' ? 'Dipinjam' : 'Perbaikan') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection