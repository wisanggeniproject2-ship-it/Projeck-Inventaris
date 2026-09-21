@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-2xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Detail Pengajuan Penghapusan</h1>
        <a href="{{ route('super_admin.disposals.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div>
            <label class="text-sm text-gray-500">Barang</label>
            <p class="font-bold text-lg">{{ $disposal->item->name ?? '-' }}</p>
            <p class="text-xs text-gray-400 font-mono">{{ $disposal->item->code ?? '-' }}</p>
        </div>

        <div>
            <label class="text-sm text-gray-500">Diajukan Oleh</label>
            <p class="font-medium">{{ $disposal->user->name ?? '-' }}</p>
        </div>

        <div>
            <label class="text-sm text-gray-500">Jumlah Unit Rusak</label>
            <p class="font-bold">{{ $disposal->quantity }} unit</p>
        </div>

        <div>
            <label class="text-sm text-gray-500">Tanggal Pengajuan</label>
            <p>{{ $disposal->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <div>
            <label class="text-sm text-gray-500">Alasan Penghapusan</label>
            <p class="bg-gray-50 rounded-lg p-3 text-sm">{{ $disposal->reason }}</p>
        </div>

        @if($disposal->photos && count($disposal->photos) > 0)
        <div>
            <label class="text-sm text-gray-500">Foto Bukti Kerusakan</label>
            <div class="grid grid-cols-3 gap-2 mt-2">
                @foreach($disposal->photos as $photo)
                <a href="{{ \Storage::url($photo) }}" target="_blank">
                    <img src="{{ \Storage::url($photo) }}" class="rounded-lg border object-cover w-full h-24 hover:opacity-80 transition">
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <div>
            <label class="text-sm text-gray-500">Status</label>
            <p>
                <span class="px-2 py-1 text-xs rounded-full
                    {{ $disposal->status == 'pending' ? 'bg-yellow-100 text-yellow-700' :
                       ($disposal->status == 'approved' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                    {{ $disposal->status == 'pending' ? 'Menunggu Konfirmasi' :
                       ($disposal->status == 'approved' ? 'Disetujui (Stok Dikurangi)' : 'Ditolak') }}
                </span>
            </p>
        </div>

        @if($disposal->status == 'approved')
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-700">
                <i class="fas fa-circle-check mr-1"></i>
                Disetujui oleh {{ $disposal->approver->name ?? '-' }} pada {{ $disposal->approved_at?->format('d/m/Y H:i') }}.
                Stok barang sudah dikurangi sebanyak {{ $disposal->quantity }} unit.
            </div>
        @elseif($disposal->status == 'rejected')
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-sm text-gray-700">
                <i class="fas fa-circle-xmark mr-1"></i>
                Ditolak. Alasan: {{ $disposal->rejection_reason ?? '-' }}
            </div>
        @endif

        @if($disposal->isPending())
            <div class="pt-4 border-t space-y-4">
                <!-- Tombol Approve -->
                <form action="{{ route('super_admin.disposals.approve', $disposal) }}" method="POST"
                      onsubmit="return confirm('Yakin mau setujui? {{ $disposal->quantity }} unit barang ini akan dikurangi dari stok.');">
                    @csrf
                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2.5 rounded-lg transition">
                        <i class="fas fa-check mr-2"></i>Setujui & Kurangi Stok
                    </button>
                </form>

                <!-- Tombol Reject -->
                <form action="{{ route('super_admin.disposals.reject', $disposal) }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label class="block text-sm font-medium mb-1">Alasan Penolakan (jika ditolak)</label>
                        <textarea name="rejection_reason" rows="2"
                                  class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:border-gray-500"
                                  placeholder="Isi ini kalau mau menolak pengajuan..."></textarea>
                        @error('rejection_reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2.5 rounded-lg transition">
                        <i class="fas fa-xmark mr-2"></i>Tolak Pengajuan
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection