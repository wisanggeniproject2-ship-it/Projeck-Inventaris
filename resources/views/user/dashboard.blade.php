@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-6">Dashboard User - {{ auth()->user()->unit->name }}</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Menunggu</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                </div>
                <i class="fas fa-clock text-3xl text-yellow-500"></i>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Disetujui</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</p>
                </div>
                <i class="fas fa-check-circle text-3xl text-green-500"></i>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Dikembalikan</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['returned'] }}</p>
                </div>
                <i class="fas fa-undo-alt text-3xl text-blue-500"></i>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Peminjaman</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['active'] }}</p>
                </div>
                <i class="fas fa-exchange-alt text-3xl text-purple-500"></i>
            </div>
        </div>
    </div>

    <!-- My Circulations -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="font-semibold text-lg">Peminjamanku</h3>
            <a href="{{ route('user.circulations.index') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                Lihat Semua →
            </a>
        </div>
        <div class="overflow-x-auto">
            @if($myCirculations->count() > 0)
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Pinjam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tenggat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($myCirculations as $circulation)
                    <tr class="border-t">
                        <td class="px-6 py-4">
                            {{ $circulation->item->name }}
                            <br><small class="text-gray-500">{{ $circulation->item->code }}</small>
                        </td>
                        <td class="px-6 py-4">{{ $circulation->item->unit->name }}</td>
                        <td class="px-6 py-4">{{ $circulation->borrow_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">{{ $circulation->expected_return_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $circulation->status == 'approved' ? 'bg-green-100 text-green-700' : 
                                   ($circulation->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                   ($circulation->status == 'returned' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                                {{ ucfirst($circulation->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('user.items.show', $circulation->item_id) }}" 
                                   class="text-blue-600 hover:text-blue-800" title="Detail Barang">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($circulation->status == 'approved')
                                <a href="{{ route('user.circulations.return', $circulation) }}" 
                                   class="text-green-600 hover:text-green-800" title="Kembalikan Barang"
                                   onclick="event.preventDefault(); if(confirm('Yakin ingin mengembalikan barang ini?')) document.getElementById('return-form-{{ $circulation->id }}').submit();">
                                    <i class="fas fa-undo-alt"></i>
                                </a>
                                <form id="return-form-{{ $circulation->id }}" 
                                      action="{{ route('user.circulations.return', $circulation) }}" 
                                      method="POST" style="display: none;">
                                    @csrf
                                    @method('PUT')
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-inbox text-4xl mb-2 block"></i>
                <p>Belum ada peminjaman</p>
                <a href="{{ route('user.circulations.create') }}" class="text-blue-500 hover:text-blue-700 mt-2 inline-block">
                    Ajukan Peminjaman →
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Available Items from ALL UNITS -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="font-semibold text-lg">Barang Tersedia dari Semua Unit</h3>
            <a href="{{ route('user.items.index') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                Lihat Semua →
            </a>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @forelse($availableItems as $item)
                <div class="border rounded-lg p-4 hover:shadow-lg transition">
                    <div class="flex justify-between items-start">
                        <h4 class="font-semibold">{{ $item->name }}</h4>
                        <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $item->unit->name }}</span>
                    </div>
                    <p class="text-sm text-gray-600">Kode: {{ $item->code }}</p>
                    <p class="text-sm text-gray-600">Lokasi: {{ $item->location }}</p>
                    <a href="{{ route('user.circulations.create', ['item' => $item->id]) }}" 
                       class="mt-2 inline-block text-blue-500 text-sm hover:text-blue-700">
                        <i class="fas fa-hand-paper mr-1"></i>Pinjam →
                    </a>
                </div>
                @empty
                <div class="col-span-full text-center py-8 text-gray-500">
                    <i class="fas fa-box-open text-4xl mb-2 block"></i>
                    Tidak ada barang tersedia saat ini
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection