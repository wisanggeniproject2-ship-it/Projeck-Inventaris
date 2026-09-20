@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Riwayat Pengajuan Penghapusan</h1>
        <a href="{{ route('user.disposals.create') }}" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i>Ajukan Baru
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alasan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Ajuan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan Admin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($disposals as $disposal)
                    <tr class="border-t">
                        <td class="px-4 py-3">
                            <p class="font-medium">{{ $disposal->item->name ?? '-' }}</p>
                            <p class="text-xs text-gray-400 font-mono">{{ $disposal->item->code ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 max-w-xs">{{ $disposal->reason }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $disposal->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $disposal->status == 'pending' ? 'bg-yellow-100 text-yellow-700' :
                                   ($disposal->status == 'approved' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                                {{ $disposal->status == 'pending' ? 'Menunggu Konfirmasi' :
                                   ($disposal->status == 'approved' ? 'Disetujui (Dihapus)' : 'Ditolak') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $disposal->rejection_reason ?: '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
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