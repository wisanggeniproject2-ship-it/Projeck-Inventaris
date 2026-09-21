@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-5xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Pengajuan Penghapusan Aset</h1>
    </div>

    <!-- Filter Status -->
    <div class="bg-white rounded-lg shadow p-4 mb-4">
        <form method="GET" class="flex flex-wrap gap-2">
            <a href="{{ route('super_admin.disposals.index') }}"
               class="px-3 py-1.5 rounded-lg text-sm {{ !request('status') ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Semua
            </a>
            <a href="{{ route('super_admin.disposals.index', ['status' => 'pending']) }}"
               class="px-3 py-1.5 rounded-lg text-sm {{ request('status') == 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Menunggu Konfirmasi
            </a>
            <a href="{{ route('super_admin.disposals.index', ['status' => 'approved']) }}"
               class="px-3 py-1.5 rounded-lg text-sm {{ request('status') == 'approved' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Disetujui
            </a>
            <a href="{{ route('super_admin.disposals.index', ['status' => 'rejected']) }}"
               class="px-3 py-1.5 rounded-lg text-sm {{ request('status') == 'rejected' ? 'bg-gray-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Ditolak
            </a>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diajukan Oleh</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alasan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($disposals as $disposal)
                    <tr class="border-t">
                        <td class="px-4 py-3">
                            <p class="font-medium">{{ $disposal->item->name ?? '-' }}</p>
                            <p class="text-xs text-gray-400 font-mono">{{ $disposal->item->code ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold">{{ $disposal->quantity }} unit</td>
                        <td class="px-4 py-3 text-sm">{{ $disposal->user->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">{{ $disposal->reason }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $disposal->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $disposal->status == 'pending' ? 'bg-yellow-100 text-yellow-700' :
                                   ($disposal->status == 'approved' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                                {{ $disposal->status == 'pending' ? 'Menunggu Konfirmasi' :
                                   ($disposal->status == 'approved' ? 'Disetujui (Stok Dikurangi)' : 'Ditolak') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('super_admin.disposals.show', $disposal) }}"
                               class="text-teal-600 hover:text-teal-800 text-sm font-medium">
                                <i class="fas fa-eye mr-1"></i>Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            Belum ada pengajuan penghapusan aset.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $disposals->links() }}
    </div>
</div>
@endsection