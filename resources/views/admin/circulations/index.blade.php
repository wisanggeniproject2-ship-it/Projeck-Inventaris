@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Manajemen Sirkulasi</h1>
    </div>

    <!-- Filter - Responsive -->
    <div class="mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-2">
            <select name="status" class="px-4 py-2 border rounded-lg w-full sm:w-auto">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
            </select>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 w-full sm:w-auto">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
        </form>
    </div>

    <!-- ✅ TABEL RESPONSIVE -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Barang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Peminjam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Tgl Pinjam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Tenggat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($circulations as $circulation)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $circulation->item->name }}</div>
                                <div class="text-xs text-gray-500">{{ $circulation->item->code }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $circulation->borrower_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $circulation->item->unit->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $circulation->borrow_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $circulation->expected_return_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full
                                    {{ $circulation->status == 'approved' ? 'bg-green-100 text-green-700' : 
                                       ($circulation->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                       ($circulation->status == 'returned' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                                    {{ ucfirst($circulation->status) }}
                                </span>

                                {{-- 🔥 Tampilkan alasan reject di baris yang rejected --}}
                                @if($circulation->status == 'rejected' && $circulation->rejection_reason)
                                    <div class="mt-2 p-2 bg-red-50 border-l-4 border-red-400 rounded text-xs text-red-700 max-w-[220px]">
                                        <p class="font-semibold mb-0.5">
                                            <i class="fas fa-info-circle mr-1"></i>Alasan:
                                        </p>
                                        <p class="break-words">{{ $circulation->rejection_reason }}</p>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex gap-2">
                                    <!-- Detail -->
                                    <a href="{{ route('super_admin.circulations.show', $circulation) }}" 
                                       class="text-blue-600 hover:text-blue-800" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($circulation->status == 'pending')
                                        <form action="{{ route('super_admin.circulations.approve', $circulation) }}" 
                                              method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800"
                                                    onclick="return confirm('Setujui peminjaman ini?')" title="Setujui">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>

                                        {{-- 🔥TombolReject→bukamodal --}}
                                        <button type="button"
                                                onclick="openRejectModal(
                                                    {{ $circulation->id }},
                                                    '{{ addslashes($circulation->item->name ?? '') }}',
                                                    '{{ addslashes($circulation->borrower_name ?? '') }}'
                                                )"
                                                class="text-red-600 hover:text-red-800"
                                                title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    
                                    @if($circulation->status == 'approved')
                                        <form action="{{ route('super_admin.circulations.return', $circulation) }}" 
                                              method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-blue-600 hover:text-blue-800"
                                                    onclick="return confirm('Tandai barang sudah dikembalikan?')" title="Kembalikan">
                                                <i class="fas fa-undo-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-6">
        {{ $circulations->links() }}
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL REJECT — Isi Alasan Penolakan                          --}}
{{-- ============================================================ --}}
<div id="rejectModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeRejectModal()"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden" style="animation: modalIn 0.25s ease-out;">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-5 py-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                <i class="fas fa-times-circle text-white text-lg"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="text-white font-bold text-base">Tolak Peminjaman</h3>
                <p class="text-red-100 text-xs">Berikan alasan penolakan yang jelas</p>
            </div>
            <button type="button" onclick="closeRejectModal()" 
                    class="text-white/70 hover:text-white transition w-8 h-8 rounded-lg hover:bg-white/10 flex items-center justify-center">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Body --}}
        <form id="rejectForm" method="POST" class="p-5">
            @csrf

            {{-- Info barang --}}
            <div class="bg-gray-50 rounded-xl p-3 mb-4 border border-gray-100">
                <p class="text-xs text-gray-500 mb-1">Barang yang ditolak:</p>
                <p class="font-semibold text-gray-800 text-sm" id="rejectItemName">-</p>
                <p class="text-xs text-gray-500 mt-1">
                    Peminjam: <span class="font-medium" id="rejectBorrowerName">-</span>
                </p>
            </div>

            {{-- Textarea alasan --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea name="rejection_reason" 
                          id="rejectionReasonInput"
                          rows="4" 
                          maxlength="500"
                          required
                          placeholder="Contoh: Barang sedang dalam perbaikan, mohon pilih barang lain atau hubungi admin."
                          class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 text-sm resize-none"></textarea>
                <div class="flex justify-between mt-1">
                    <p class="text-xs text-gray-400">
                        <i class="fas fa-info-circle mr-1"></i>Min. 5 karakter
                    </p>
                    <p class="text-xs text-gray-400">
                        <span id="charCount">0</span>/500
                    </p>
                </div>
                @error('rejection_reason')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Quick reasons --}}
            <div class="mb-4">
                <p class="text-xs text-gray-500 mb-2">Alasan cepat (klik untuk pakai):</p>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="setReason('Barang sedang dalam perbaikan')" 
                            class="px-3 py-1.5 bg-gray-100 hover:bg-red-50 hover:text-red-600 text-gray-600 rounded-lg text-xs transition border border-gray-200 hover:border-red-300">
                        🔧 Sedang perbaikan
                    </button>
                    <button type="button" onclick="setReason('Stok barang sudah habis')" 
                            class="px-3 py-1.5 bg-gray-100 hover:bg-red-50 hover:text-red-600 text-gray-600 rounded-lg text-xs transition border border-gray-200 hover:border-red-300">
                        📦 Stok habis
                    </button>
                    <button type="button" onclick="setReason('Barang sedang dipinjam pihak lain')" 
                            class="px-3 py-1.5 bg-gray-100 hover:bg-red-50 hover:text-red-600 text-gray-600 rounded-lg text-xs transition border border-gray-200 hover:border-red-300">
                        👥 Dipinjam pihak lain
                    </button>
                    <button type="button" onclick="setReason('Data peminjam tidak lengkap')" 
                            class="px-3 py-1.5 bg-gray-100 hover:bg-red-50 hover:text-red-600 text-gray-600 rounded-lg text-xs transition border border-gray-200 hover:border-red-300">
                        📝 Data tidak lengkap
                    </button>
                    <button type="button" onclick="setReason('Barang tidak tersedia di unit ini')" 
                            class="px-3 py-1.5 bg-gray-100 hover:bg-red-50 hover:text-red-600 text-gray-600 rounded-lg text-xs transition border border-gray-200 hover:border-red-300">
                        🚫 Tidak tersedia
                    </button>
                    <button type="button" onclick="setReason('Tidak memenuhi syarat peminjaman')" 
                            class="px-3 py-1.5 bg-gray-100 hover:bg-red-50 hover:text-red-600 text-gray-600 rounded-lg text-xs transition border border-gray-200 hover:border-red-300">
                        ❌ Tidak memenuhi syarat
                    </button>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 transition text-sm font-medium">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold text-sm transition shadow-lg shadow-red-500/30 inline-flex items-center gap-2">
                    <i class="fas fa-times-circle"></i>
                    Tolak Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }
</style>

@push('scripts')
<script>
    function openRejectModal(circulationId, itemName, borrowerName) {
        const modal = document.getElementById('rejectModal');
        const form  = document.getElementById('rejectForm');
        const input = document.getElementById('rejectionReasonInput');

        // 🔥 URL action — sesuaikan dengan route super_admin
        form.action = `{{ url('super-admin/circulations') }}/${circulationId}/reject`;

        document.getElementById('rejectItemName').textContent     = itemName || '-';
        document.getElementById('rejectBorrowerName').textContent = borrowerName || '-';

        input.value = '';
        document.getElementById('charCount').textContent = '0';

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        setTimeout(() => input.focus(), 100);
    }

    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function setReason(text) {
        const input = document.getElementById('rejectionReasonInput');
        input.value = text;
        document.getElementById('charCount').textContent = text.length;
        input.focus();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('rejectionReasonInput');
        if (input) {
            input.addEventListener('input', function () {
                document.getElementById('charCount').textContent = this.value.length;
            });
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeRejectModal();
    });
</script>
@endpush
@endsection