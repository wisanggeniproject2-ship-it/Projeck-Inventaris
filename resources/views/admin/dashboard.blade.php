@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-6">Dashboard Admin</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Barang</p>
                    <p class="text-2xl font-bold">{{ $stats['total_items'] }}</p>
                </div>
                <i class="fas fa-boxes text-4xl text-blue-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Dipinjam</p>
                    <p class="text-2xl font-bold">{{ $stats['total_borrowed'] }}</p>
                </div>
                <i class="fas fa-hand-paper text-4xl text-yellow-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Pending</p>
                    <p class="text-2xl font-bold">{{ $stats['total_pending'] }}</p>
                </div>
                <i class="fas fa-clock text-4xl text-orange-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Unit</p>
                    <p class="text-2xl font-bold">{{ $stats['total_units'] }}</p>
                </div>
                <i class="fas fa-building text-4xl text-green-500"></i>
            </div>
        </div>
    </div>

    <!-- Recent Items -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-lg">Barang Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            @include('admin.items._table', ['items' => $recentItems])
        </div>
    </div>

    <!-- Recent Circulations -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-lg">Peminjaman Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            @include('admin.circulations._table', ['circulations' => $recentCirculations])
        </div>
    </div>
</div>
@endsection