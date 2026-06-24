@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Riwayat Peminjaman Saya</h1>
        <a href="{{ route('user.circulations.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
            <i class="fas fa-plus mr-2"></i>Ajukan Peminjaman
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">Barang</th>
                    <th class="px-6 py-3 text-left">Tanggal Pinjam</th>
                    <th class="px-6 py-3 text-left">Tenggat</th>
                    <th class="px-6 py-3 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($circulations as $circulation)
                <tr class="border-t">
                    <td class="px-6 py-4">
                        {{ $circulation->item->name }}
                        <br><small class="text-gray-500">{{ $circulation->item->code }}</small>
                    </td>
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
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $circulations->links() }}
    </div>
</div>
@endsection