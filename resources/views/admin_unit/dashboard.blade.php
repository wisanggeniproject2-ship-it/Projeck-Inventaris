@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Dashboard Admin Unit</h1>
        <div class="text-sm text-gray-500">
            <i class="fas fa-building mr-1"></i>
            {{ auth()->user()->unit->name ?? 'Unit' }}
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
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
                    <p class="text-gray-500 text-sm">Dikembalikan</p>
                    <p class="text-2xl font-bold">{{ $stats['total_returned'] }}</p>
                </div>
                <i class="fas fa-undo-alt text-3xl text-green-500"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total User</p>
                    <p class="text-2xl font-bold">{{ $stats['total_users'] }}</p>
                </div>
                <i class="fas fa-users text-3xl text-purple-500"></i>
            </div>
        </div>
    </div>
    
    <!-- Recent Items -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="font-semibold text-lg">Barang Terbaru</h3>
            <a href="{{ route('admin_unit.items.index') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                Lihat Semua →
            </a>
        </div>
        <div class="overflow-x-auto">
            @if($recentItems->count() > 0)
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentItems as $item)
                    <tr>
                        <td class="px-6 py-4">{{ $item->code }}</td>
                        <td class="px-6 py-4">{{ $item->name }}</td>
                        <td class="px-6 py-4">{{ $item->category->name }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : 
                                   ($item->status == 'borrowed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ $item->status == 'available' ? 'Tersedia' : ($item->status == 'borrowed' ? 'Dipinjam' : 'Perbaikan') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin_unit.items.show', $item) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-box-open text-4xl mb-2"></i>
                <p>Belum ada barang di unit ini</p>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Recent Circulations -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="font-semibold text-lg">Peminjaman Terbaru</h3>
            <a href="{{ route('admin_unit.circulations.index') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                Lihat Semua →
            </a>
        </div>
        <div class="overflow-x-auto">
            @if($recentCirculations->count() > 0)
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peminjam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Pinjam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentCirculations as $circulation)
                    <tr>
                        <td class="px-6 py-4">{{ $circulation->item->name }}</td>
                        <td class="px-6 py-4">{{ $circulation->borrower_name }}</td>
                        <td class="px-6 py-4">{{ $circulation->borrow_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $circulation->status == 'approved' ? 'bg-green-100 text-green-700' : 
                                   ($circulation->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                   ($circulation->status == 'returned' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                                {{ ucfirst($circulation->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-exchange-alt text-4xl mb-2"></i>
                <p>Belum ada peminjaman di unit ini</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection