@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-6">Dashboard User</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Ajukan Peminjaman</p>
                    <p class="text-2xl font-bold">Barang Tersedia</p>
                </div>
                <i class="fas fa-box-open text-4xl text-green-500"></i>
            </div>
            <a href="{{ route('user.circulations.create') }}" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-hand-paper mr-2"></i>Ajukan Sekarang
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Peminjaman Aktif</p>
                    <p class="text-2xl font-bold">{{ $activeLoans ?? 0 }}</p>
                </div>
                <i class="fas fa-clock text-4xl text-yellow-500"></i>
            </div>
            <a href="{{ route('user.circulations.index') }}" class="mt-4 inline-block text-blue-500 hover:text-blue-700">
                Lihat Riwayat →
            </a>
        </div>
    </div>

    <!-- Available Items -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-lg">Barang Tersedia di Unit {{ auth()->user()->unit->name }}</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($availableItems ?? [] as $item)
                <div class="border rounded-lg p-4">
                    <h4 class="font-semibold">{{ $item->name }}</h4>
                    <p class="text-sm text-gray-600">Kode: {{ $item->code }}</p>
                    <p class="text-sm text-gray-600">Lokasi: {{ $item->location }}</p>
                    <a href="{{ route('user.circulations.create', ['item' => $item->id]) }}" class="mt-2 inline-block text-blue-500 text-sm">
                        Pinjam →
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection