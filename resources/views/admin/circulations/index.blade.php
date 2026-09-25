@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                <i class="fas fa-right-left text-brand-500 mr-2"></i>Manajemen Sirkulasi
            </h1>
            <p class="text-sm text-gray-500 mt-1">Kelola semua peminjaman barang</p>
        </div>
    </div>

    {{-- RINGKASAN TENGGAT --}}
    @php
        $overdueCount = \App\Models\Circulation::overdue()->count();
        $dueTodayCount = \App\Models\Circulation::dueToday()->count();
    @endphp

    @if($overdueCount > 0 || $dueTodayCount > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
        @if($overdueCount > 0)
        <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-red-50 to-rose-50 border-2 border-red-200 rounded-2xl">
            <div class="w-12 h-12 rounded-xl bg-red-500 flex items-center justify-center shrink-0 shadow-lg shadow-red-500/30">
                <i class="fas fa-exclamation-triangle text-white text-xl"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-red-700 uppercase tracking-wide">Terlambat</p>
                <p class="text-2xl font-bold text-red-600 leading-tight">{{ $overdueCount }}</p>
                <p class="text-[11px] text-red-500">Barang belum dikembalikan</p>
            </div>
            <a href="?status=approved" class="text-red-600 hover:text-red-800 transition">
                <i class="fas fa-arrow-right text-xl"></i>
            </a>
        </div>
        @endif

        @if($dueTodayCount > 0)
        <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-200 rounded-2xl">
            <div class="w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center shrink-0 shadow-lg shadow-amber-500/30">
                <i class="fas fa-clock text-white text-xl"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide">Tenggat Hari Ini</p>
                <p class="text-2xl font-bold text-amber-600 leading-tight">{{ $dueTodayCount }}</p>
                <p class="text-[11px] text-amber-500">Harus dikembalikan hari ini</p>
            </div>
            <a href="?status=approved" class="text-amber-600 hover:text-amber-800 transition">
                <i class="fas fa-arrow-right text-xl"></i>
            </a>
        </div>
        @endif
    </div>
    @endif

    {{-- FILTER --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-5">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <select name="status" 
                    class="px-4 py-2.5 border-2 border-gray-200 rounded-xl w-full sm:w-auto text-sm focus:outline-none focus:border-brand-500 transition">
                <option value="">📋 Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                <option value="return_pending" {{ request('status') == 'return_pending' ? 'selected' : '' }}>🔄 Return Pending</option>
                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>📦 Returned</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
            </select>
            <button type="submit" 
                    class="bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-white px-5 py-2.5 rounded-xl transition-all text-sm font-medium shadow-sm hover:shadow-md hover:-translate-y-0.5 w-full sm:w-auto inline-flex items-center justify-center gap-2">
                <i class="fas fa-filter"></i>
                Filter
            </button>
            @if(request('status'))
            <a href="{{ route('super_admin.circulations.index') }}" 
               class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2.5 rounded-xl transition inline-flex items-center justify-center"
               title="Reset filter">
                <i class="fas fa-times"></i>
            </a>
            @endif
        </form>
    </div>

    {{-- TABEL --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Barang</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Peminjam</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Unit</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                <i class="fas fa-sign-out-alt mr-1 text-gray-400"></i>Jam Pinjam
                            </th>
                            <th class="px-4 sm:px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                <i class="fas fa-hourglass-half mr-1 text-gray-400"></i>Tenggat
                            </th>
                            <th class="px-4 sm:px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Status</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($circulations as $circulation)
                        @php
                            $isOverdue = method_exists($circulation, 'isOverdue')
                                ? ($circulation->isOverdue() ?? false)
                                : false;

                            $statusClasses = [
                                'approved'       => 'bg-green-100 text-green-700',
                                'pending'        => 'bg-yellow-100 text-yellow-700',
                                'return_pending' => 'bg-blue-100 text-blue-700',
                                'returned'       => 'bg-gray-100 text-gray-700',
                                'rejected'       => 'bg-red-100 text-red-700',
                            ];
                            $statusIcons = [
                                'pending'        => '⏳',
                                'approved'       => '✅',
                                'return_pending' => '🔄',
                                'returned'       => '📦',
                                'rejected'       => '❌',
                            ];
                            $statusClass = $statusClasses[$circulation->status] ?? 'bg-gray-100 text-gray-700';
                            $statusIcon  = $statusIcons[$circulation->status] ?? '';
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition
                            {{ $circulation->status == 'pending' ? 'bg-yellow-50/40' : '' }}
                            {{ $circulation->status == 'return_pending' ? 'bg-blue-50/40' : '' }}
                            {{ $isOverdue ? 'bg-red-50/60' : '' }}">
                            
                            {{-- BARANG --}}
                            <td class="px-4 sm:px-6 py-4">
                                <div class="text-sm font-medium text-gray-800">{{ $circulation->item->name ?? '-' }}</div>

                                {{-- 🔥 KODE STOK SPESIFIK (badge warna brand) --}}
                                @if($circulation->stock_code)
                                    <div class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 rounded-md text-[10px] font-mono font-bold break-all"
                                         style="background: #0F6B5F15; color: #0F6B5F;"
                                         title="Kode stok unit yang dipinjam">
                                        <i class="fas fa-qrcode text-[9px]"></i>
                                        {{ $circulation->stock_code }}
                                    </div>
                                @endif
                            </td>

                            {{-- PEMINJAM --}}
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                        {{ strtoupper(substr($circulation->borrower_name ?? '?', 0, 1)) }}
                                    </div>
                                    <span class="text-sm text-gray-700">{{ $circulation->borrower_name }}</span>
                                </div>
                            </td>

                            {{-- UNIT --}}
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 text-xs font-medium bg-teal-50 text-teal-700 px-2 py-1 rounded-lg border border-teal-100">
                                    <i class="fas fa-building text-[9px]"></i>
                                    {{ $circulation->item->unit->name ?? '-' }}
                                </span>
                            </td>

                            {{-- JAM PINJAM --}}
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                @if($circulation->borrow_date)
                                    <div class="text-sm font-medium text-gray-800">
                                        <i class="fas fa-calendar-alt text-gray-400 text-xs mr-1"></i>
                                        {{ $circulation->borrow_date->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        <i class="fas fa-clock text-gray-400 text-[10px] mr-1"></i>
                                        {{ $circulation->borrow_date->format('H:i') }} WIB
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>

                            {{-- TENGGAT --}}
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                @if($circulation->expected_return_date)
                                    <div class="text-sm font-medium {{ $isOverdue ? 'text-red-600' : 'text-gray-800' }}">
                                        <i class="fas fa-calendar-alt text-gray-400 text-xs mr-1"></i>
                                        {{ $circulation->expected_return_date->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs {{ $isOverdue ? 'text-red-600 font-bold' : 'text-gray-600' }} mt-0.5">
                                        <i class="fas fa-clock text-[10px] mr-1"></i>
                                        Jam {{ $circulation->expected_return_date->format('H:i') }} WIB
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-1">
                                        <i class="fas fa-user text-[9px] mr-1"></i>
                                        {{ $circulation->borrower_name }}
                                    </div>

                                    @if($isOverdue)
                                        <span class="inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 text-[10px] font-bold rounded-full bg-red-100 text-red-700 border border-red-200">
                                            <i class="fas fa-exclamation-triangle text-[9px]"></i>
                                            TERLAMBAT
                                            @if(method_exists($circulation, 'overdueDuration'))
                                                {{ $circulation->overdueDuration() }}
                                            @endif
                                        </span>
                                    @elseif(in_array($circulation->status, ['approved', 'return_pending']))
                                        @php
                                            $sisa = method_exists($circulation, 'timeUntilDue')
                                                ? $circulation->timeUntilDue()
                                                : null;
                                        @endphp
                                        @if($sisa)
                                            <span class="inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 text-[10px] font-medium rounded-full bg-green-100 text-green-700 border border-green-200">
                                                <i class="fas fa-hourglass-half text-[9px]"></i>
                                                Sisa {{ $sisa }}
                                            </span>
                                        @endif
                                    @endif
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>

                            {{-- STATUS --}}
                            <td class="px-4 sm:px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                    {{ $statusIcon }} {{ ucfirst(str_replace('_', ' ', $circulation->status)) }}
                                </span>

                                @if($circulation->status == 'rejected' && $circulation->rejection_reason)
                                    <div class="mt-2 p-2 bg-red-50 border-l-4 border-red-400 rounded text-xs text-red-700 max-w-[220px]">
                                        <p class="font-semibold mb-0.5">
                                            <i class="fas fa-info-circle mr-1"></i>Alasan:
                                        </p>
                                        <p class="break-words leading-snug">{{ $circulation->rejection_reason }}</p>
                                        @if($circulation->rejected_at)
                                            <p class="mt-1 text-[10px] text-red-500/80">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $circulation->rejected_at->format('d/m/Y H:i') }}
                                            </p>
                                        @endif
                                    </div>
                                @endif

                                @if($circulation->status == 'return_pending')
                                    <div class="mt-2 p-2 bg-blue-50 border-l-4 border-blue-400 rounded text-xs text-blue-700 max-w-[220px]">
                                        <p class="font-semibold mb-0.5">
                                            <i class="fas fa-info-circle mr-1"></i>Info:
                                        </p>
                                        <p class="break-words leading-snug">
                                            User sudah mengajukan pengembalian, tinggal dikonfirmasi.
                                        </p>
                                    </div>
                                @endif
                            </td>

                            {{-- AKSI --}}
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <div class="flex gap-2 items-center">
                                    {{-- Detail --}}
                                    <a href="{{ route('super_admin.circulations.show', $circulation) }}" 
                                       class="text-blue-600 hover:text-blue-800 transition" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    {{-- Pending: Approve + Reject --}}
                                    @if($circulation->status == 'pending')
                                        <form action="{{ route('super_admin.circulations.approve', $circulation) }}" 
                                              method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800 transition"
                                                    onclick="return confirm('Setujui peminjaman ini?')" title="Setujui">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>

                                        <button type="button"
                                                onclick="openRejectModal(
                                                    {{ $circulation->id }},
                                                    '{{ addslashes($circulation->item->name ?? '') }}',
                                                    '{{ addslashes($circulation->borrower_name ?? '') }}'
                                                )"
                                                class="text-red-600 hover:text-red-800 transition"
                                                title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    
                                    {{-- Approved: Mark Returned --}}
                                    @if($circulation->status == 'approved')
                                        <form action="{{ route('super_admin.circulations.return', $circulation) }}" 
                                              method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-blue-600 hover:text-blue-800 transition"
                                                    onclick="return confirm('Tandai barang sudah dikembalikan?')" title="Tandai Kembali">
                                                <i class="fas fa-undo-alt"></i>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Return Pending: Konfirmasi --}}
                                    @if($circulation->status == 'return_pending')
                                        <form action="{{ route('super_admin.circulations.confirm-return', $circulation) }}" 
                                              method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800 transition inline-flex items-center gap-1"
                                                    onclick="return confirm('Konfirmasi bahwa barang sudah dikembalikan?')" 
                                                    title="Konfirmasi Pengembalian">
                                                <i class="fas fa-check-double"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <div class="w-20 h-20 mx-auto mb-3 rounded-full bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-inbox text-3xl text-gray-300"></i>
                                </div>
                                <p class="text-gray-600 font-medium">Belum ada data sirkulasi</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- PAGINATION --}}
    @if($circulations->hasPages())
    <div class="mt-6">
        {{ $circulations->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- ============================================================ --}}
{{-- MODAL REJECT — Isi Alasan Penolakan                          --}}
{{-- ============================================================ --}}
<div id="rejectModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeRejectModal()"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden" style="animation: modalIn 0.25s ease-out;">
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

        <form id="rejectForm" method="POST" class="p-5">
            @csrf

            <div class="bg-gray-50 rounded-xl p-3 mb-4 border border-gray-100">
                <p class="text-xs text-gray-500 mb-1">Barang yang ditolak:</p>
                <p class="font-semibold text-gray-800 text-sm" id="rejectItemName">-</p>
                <p class="text-xs text-gray-500 mt-1">
                    Peminjam: <span class="font-medium" id="rejectBorrowerName">-</span>
                </p>
            </div>

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