@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-2xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Ajukan Penghapusan Aset</h1>
        <a href="{{ route('user.disposals.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Riwayat Pengajuan
        </a>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start gap-3">
            <i class="fas fa-circle-info text-blue-500 mt-0.5"></i>
            <p class="text-sm text-blue-700">
                Ajukan barang yang rusak parah/tidak bisa dipakai lagi untuk dihapus dari daftar aset.
                Pengajuan akan ditinjau oleh Super Admin sebelum barang resmi dihapus.
            </p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('user.disposals.store') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Pilih Barang *</label>
                    <select name="item_id" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($items as $item)
                        <option value="{{ $item->id }}"
                            {{ old('item_id', $selectedItem?->id) == $item->id ? 'selected' : '' }}
                            {{ $item->hasPendingDisposalRequest() ? 'disabled' : '' }}>
                            {{ $item->name }} ({{ $item->code }})
                            @if($item->hasPendingDisposalRequest()) — sudah ada pengajuan pending @endif
                        </option>
                        @endforeach
                    </select>
                    @error('item_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Alasan Penghapusan *</label>
                    <textarea name="reason" rows="4" required minlength="10"
                              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                              placeholder="Jelaskan kondisi barang, misal: layar TV pecah dan tidak bisa menyala sama sekali.">{{ old('reason') }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Minimal 10 karakter, jelaskan kondisi barang sedetail mungkin.</p>
                    @error('reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <a href="{{ route('user.disposals.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">
                    <i class="fas fa-paper-plane mr-2"></i>Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection