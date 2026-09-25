@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-3xl px-4 sm:px-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
                <i class="fas fa-hand-holding text-brand-500 mr-2"></i>Ajukan Peminjaman
            </h1>
            <p class="text-sm text-gray-500 mt-1">Isi data peminjaman di bawah ini</p>
        </div>
        <a href="{{ route('user.items.index') }}" 
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-brand-600 transition">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Daftar Barang
        </a>
    </div>

    {{-- INFO BARANG YANG DIPILIH --}}
    @if($selectedItem)
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl p-4 sm:p-5 mb-6 shadow-sm">
        <div class="flex items-start gap-4">
            {{-- Gambar --}}
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden border-2 border-white shadow-md shrink-0 bg-gray-100">
                <img src="{{ $selectedItem->image_url }}" 
                     alt="{{ $selectedItem->name }}" 
                     class="w-full h-full object-cover">
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold rounded-full bg-green-500 text-white">
                        <i class="fas fa-check-circle text-[10px]"></i>
                        Barang Dipilih
                    </span>
                </div>
                <h3 class="font-bold text-lg text-gray-800 mb-1">{{ $selectedItem->name }}</h3>
                
                <div class="flex flex-wrap gap-2 text-xs mb-2">
                    <span class="inline-flex items-center gap-1 font-medium bg-blue-50 text-blue-700 px-2 py-1 rounded-lg border border-blue-100">
                        <i class="fas fa-tag text-[9px]"></i>{{ $selectedItem->category->name ?? '-' }}
                    </span>
                    <span class="inline-flex items-center gap-1 font-medium bg-teal-50 text-teal-700 px-2 py-1 rounded-lg border border-teal-100">
                        <i class="fas fa-building text-[9px]"></i>{{ $selectedItem->unit->name ?? '-' }}
                    </span>
                    <span class="inline-flex items-center gap-1 font-medium bg-purple-50 text-purple-700 px-2 py-1 rounded-lg border border-purple-100">
                        <i class="fas fa-boxes text-[9px]"></i>Stok Tersedia: {{ $availableStockCodes->count() ?? 0 }}
                    </span>
                </div>

                <p class="text-xs text-gray-500">
                    <i class="fas fa-barcode mr-1"></i>
                    <span class="font-mono">{{ $selectedItem->code }}</span>
                </p>
            </div>
        </div>

        {{-- Info: tidak bisa diubah --}}
        <div class="mt-3 pt-3 border-t border-green-200 flex items-start gap-2 text-xs text-green-700">
            <i class="fas fa-lock mt-0.5"></i>
            <p>
                Barang dan unit sudah dipilih dan <strong>tidak dapat diubah</strong>.
                Kalau mau pilih barang lain, kembali ke <a href="{{ route('user.items.index') }}" class="underline font-medium">Daftar Barang</a>.
            </p>
        </div>
    </div>
    @endif

    {{-- FORM --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-7">
        <form action="{{ route('user.circulations.store') }}" method="POST">
            @csrf

            {{-- 🔥 HIDDEN INPUT — barang & unit dikirim otomatis --}}
            <input type="hidden" name="item_id" value="{{ $selectedItem->id ?? '' }}">
            <input type="hidden" name="unit_id" value="{{ $selectedItem->unit_id ?? '' }}">

            <div class="space-y-5">

                {{-- ============================================================ --}}
                {{-- 🔥 WAKTU SEKARANG (LIVE) — cuma info, bukan input          --}}
                {{-- ============================================================ --}}
                <div class="bg-gradient-to-r from-teal-50 to-cyan-50 border-2 border-teal-200 rounded-xl p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center shrink-0">
                            <i class="fas fa-clock text-teal-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-teal-700 uppercase tracking-wide">
                                <span class="relative inline-flex items-center gap-1.5">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500"></span>
                                    </span>
                                    Waktu Sekarang
                                </span>
                            </p>
                            <p class="text-lg font-bold text-teal-800 mt-0.5">
                                <span id="liveDate">{{ now()->format('d M Y') }}</span> — 
                                <span class="text-2xl tabular-nums" id="liveClock">{{ now()->format('H:i:s') }}</span>
                                <span class="text-sm font-medium">WIB</span>
                            </p>
                            <p class="text-[11px] text-teal-600 mt-0.5">
                                <i class="fas fa-info-circle mr-1"></i>
                                Referensi waktu saat ini (WIB)
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Info Terkunci (readonly, biar user tahu) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Barang (Readonly) --}}
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">
                            <i class="fas fa-boxes-stacked text-brand-500 mr-1.5"></i>
                            Barang
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   value="{{ $selectedItem->name ?? '-' }}"
                                   readonly
                                   class="w-full px-3 py-2.5 pr-10 border-2 border-gray-200 bg-gray-50 rounded-xl text-sm text-gray-600 cursor-not-allowed focus:outline-none">
                            <i class="fas fa-lock absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        </div>
                    </div>

                    {{-- Unit (Readonly) --}}
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">
                            <i class="fas fa-building text-brand-500 mr-1.5"></i>
                            Unit
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   value="{{ $selectedItem->unit->name ?? '-' }}"
                                   readonly
                                   class="w-full px-3 py-2.5 pr-10 border-2 border-gray-200 bg-gray-50 rounded-xl text-sm text-gray-600 cursor-not-allowed focus:outline-none">
                            <i class="fas fa-lock absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        </div>
                    </div>
                </div>

                {{-- ============================================================ --}}
                {{-- 🔥 PILIH KODE STOK SPESIFIK                                   --}}
                {{-- ============================================================ --}}
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">
                        <i class="fas fa-qrcode text-brand-500 mr-1.5"></i>
                        Pilih Kode Stok yang Mau Dipinjam <span class="text-red-500">*</span>
                    </label>

                    <div class="border-2 border-gray-200 rounded-xl p-3 max-h-72 overflow-y-auto bg-gray-50 space-y-2">
                        @forelse($availableStockCodes as $sc)
                            <label class="flex items-center gap-3 p-3 bg-white rounded-xl border-2 border-gray-200 hover:border-brand-400 hover:bg-brand-50 cursor-pointer transition has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 has-[:checked]:shadow-sm">
                                <input type="radio"
                                       name="item_stock_id"
                                       value="{{ $sc->id }}"
                                       required
                                       {{ old('item_stock_id') == $sc->id ? 'checked' : '' }}
                                       class="w-4 h-4 text-brand-500 focus:ring-brand-500 shrink-0 cursor-pointer">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5 mb-0.5">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-md text-[10px] font-bold shrink-0"
                                              style="background: #0F6B5F20; color: #0F6B5F;">
                                            {{ str_pad($sc->stock_number, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <span class="text-[10px] font-semibold text-gray-500 uppercase">
                                            Stok ke-{{ $sc->stock_number }}
                                        </span>
                                    </div>
                                    <p class="font-mono text-[11px] font-bold break-all leading-tight"
                                       style="color: #0F6B5F;">
                                        {{ $sc->stock_code }}
                                    </p>
                                </div>
                                <i class="fas fa-check-circle text-brand-500 text-lg opacity-0 transition-opacity peer-checked:opacity-100"></i>
                            </label>
                        @empty
                            <div class="text-center py-6">
                                <i class="fas fa-exclamation-circle text-2xl text-yellow-400 mb-2 block"></i>
                                <p class="text-xs text-gray-500">Tidak ada kode stok tersedia</p>
                            </div>
                        @endforelse
                    </div>

                    <p class="text-xs text-gray-400 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Pilih 1 kode stok. Kode stok lain yang sedang dipinjam orang lain tidak akan muncul di sini.
                    </p>

                    @error('item_stock_id')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Nama Peminjam --}}
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">
                        <i class="fas fa-user text-brand-500 mr-1.5"></i>
                        Nama Peminjam <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="borrower_name" 
                           value="{{ old('borrower_name', auth()->user()->name) }}" 
                           required
                           placeholder="Nama lengkap peminjam"
                           class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition">
                    @error('borrower_name') 
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>{{ $message }}
                        </p> 
                    @enderror
                </div>

                {{-- ============================================================ --}}
                {{-- 🔥 WAKTU PINJAM — Tanggal + Jam (PILIH SENDIRI)             --}}
                {{-- ============================================================ --}}
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">
                        <i class="fas fa-sign-out-alt text-brand-500 mr-1.5"></i>
                        Waktu Pinjam <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- Tanggal Pinjam --}}
                        <div>
                            <div class="relative">
                                <i class="fas fa-calendar-alt absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                                <input type="date" 
                                       name="borrow_date" 
                                       id="borrowDateInput"
                                       value="{{ old('borrow_date', now()->format('Y-m-d')) }}" 
                                       required
                                       min="{{ now()->format('Y-m-d') }}"
                                       class="w-full pl-10 pr-3 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition">
                            </div>
                            @error('borrow_date') 
                                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                </p> 
                            @enderror
                        </div>

                        {{-- 🔥 Jam Pinjam (Dropdown 24 Jam) --}}
                        <div>
                            <div class="flex items-center gap-1.5">
                                <div class="relative flex-1">
                                    <i class="fas fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none z-10"></i>
                                    <select name="borrow_hour" id="borrowHourInput" required
                                            class="w-full pl-9 pr-8 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition appearance-none bg-white cursor-pointer">
                                        @for ($h = 0; $h <= 23; $h++)
                                            @php $val = str_pad($h, 2, '0', STR_PAD_LEFT); @endphp
                                            <option value="{{ $val }}" {{ old('borrow_hour', now()->format('H')) == $val ? 'selected' : '' }}>
                                                {{ $val }}
                                            </option>
                                        @endfor
                                    </select>
                                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] pointer-events-none"></i>
                                </div>
                                <span class="text-gray-400 font-bold text-lg select-none">:</span>
                                <div class="relative flex-1">
                                    <select name="borrow_minute" id="borrowMinuteInput" required
                                            class="w-full px-3 pr-8 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition appearance-none bg-white cursor-pointer">
                                        @for ($m = 0; $m <= 59; $m++)
                                            @php $val = str_pad($m, 2, '0', STR_PAD_LEFT); @endphp
                                            <option value="{{ $val }}" {{ old('borrow_minute', now()->format('i')) == $val ? 'selected' : '' }}>
                                                {{ $val }}
                                            </option>
                                        @endfor
                                    </select>
                                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] pointer-events-none"></i>
                                </div>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1">Format 24 jam: 00:00 – 23:59</p>
                            @error('borrow_hour') 
                                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                </p> 
                            @enderror
                            @error('borrow_minute') 
                                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                </p> 
                            @enderror
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Pilih <strong>tanggal & jam</strong> kapan mau ambil barang. Bisa hari ini atau besok/lusa.
                    </p>
                </div>

                {{-- ============================================================ --}}
                {{-- 🔥 TENGGAT KEMBALI — Tanggal + Jam                          --}}
                {{-- ============================================================ --}}
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">
                        <i class="fas fa-calendar-check text-brand-500 mr-1.5"></i>
                        Tenggat Pengembalian <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- Tanggal Kembali --}}
                        <div>
                            <div class="relative">
                                <i class="fas fa-calendar-alt absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                                <input type="date" 
                                       name="expected_return_date" 
                                       id="returnDateInput"
                                       value="{{ old('expected_return_date', now()->format('Y-m-d')) }}" 
                                       required
                                       min="{{ now()->format('Y-m-d') }}"
                                       class="w-full pl-10 pr-3 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition">
                            </div>
                            @error('expected_return_date') 
                                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                </p> 
                            @enderror
                        </div>

                        {{-- 🔥 Jam Kembali (Dropdown 24 Jam) --}}
                        <div>
                            <div class="flex items-center gap-1.5">
                                <div class="relative flex-1">
                                    <i class="fas fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none z-10"></i>
                                    <select name="return_hour" id="returnHourInput" required
                                            class="w-full pl-9 pr-8 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition appearance-none bg-white cursor-pointer">
                                        @for ($h = 0; $h <= 23; $h++)
                                            @php $val = str_pad($h, 2, '0', STR_PAD_LEFT); @endphp
                                            <option value="{{ $val }}" {{ old('return_hour', '15') == $val ? 'selected' : '' }}>
                                                {{ $val }}
                                            </option>
                                        @endfor
                                    </select>
                                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] pointer-events-none"></i>
                                </div>
                                <span class="text-gray-400 font-bold text-lg select-none">:</span>
                                <div class="relative flex-1">
                                    <select name="return_minute" id="returnMinuteInput" required
                                            class="w-full px-3 pr-8 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition appearance-none bg-white cursor-pointer">
                                        @for ($m = 0; $m <= 59; $m++)
                                            @php $val = str_pad($m, 2, '0', STR_PAD_LEFT); @endphp
                                            <option value="{{ $val }}" {{ old('return_minute', '00') == $val ? 'selected' : '' }}>
                                                {{ $val }}
                                            </option>
                                        @endfor
                                    </select>
                                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] pointer-events-none"></i>
                                </div>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1">Format 24 jam: 00:00 – 23:59</p>
                            @error('return_hour') 
                                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                </p> 
                            @enderror
                            @error('return_minute') 
                                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>{{ $message }}
                                </p> 
                            @enderror
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Bisa pilih <strong>tanggal yang sama</strong> kalau cuma pinjam beberapa jam (contoh: 18/09/2026 jam 10:00 → 18/09/2026 jam 12:00).
                    </p>
                </div>

                {{-- Tujuan Peminjaman --}}
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">
                        <i class="fas fa-clipboard-list text-brand-500 mr-1.5"></i>
                        Tujuan Peminjaman <span class="text-red-500">*</span>
                    </label>
                    <textarea name="purpose" 
                              rows="4" 
                              required 
                              placeholder="Contoh: Untuk kegiatan presentasi kelas IX, untuk rapat guru, dll."
                              class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition resize-none">{{ old('purpose') }}</textarea>
                    @error('purpose') 
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>{{ $message }}
                        </p> 
                    @enderror
                </div>

                {{-- Info Box --}}
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center shrink-0">
                            <i class="fas fa-info-circle text-blue-500 text-sm"></i>
                        </div>
                        <div class="text-xs text-blue-700 leading-relaxed">
                            <p class="font-semibold mb-1">Informasi</p>
                            <p>
                                Peminjaman akan diproses oleh <strong>admin unit {{ $selectedItem->unit->name ?? '' }}</strong>.
                                Status akan berubah menjadi <span class="inline-block px-1.5 py-0.5 bg-green-100 text-green-700 rounded font-medium">approved</span> 
                                setelah disetujui.
                            </p>
                            <p class="mt-2 flex items-start gap-1.5">
                                <i class="fas fa-clock mt-0.5 text-blue-500"></i>
                                <span>Pastikan barang dikembalikan <strong>sebelum tenggat</strong> yang kamu pilih.</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TOMBOL AKSI --}}
            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-7 pt-5 border-t border-gray-100">
                <a href="{{ route('user.items.index') }}" 
                   class="px-5 py-2.5 border-2 border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 transition text-sm font-medium text-center inline-flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i>
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-xl transition-all text-sm font-medium shadow-sm hover:shadow-md hover:-translate-y-0.5 inline-flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    Ajukan Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ============================================================
    // 🔥 LIVE CLOCK — Sinkron dengan waktu server (WIB)
    // ============================================================
    (function () {
        const serverTimeStr = "{{ now()->format('Y-m-d\TH:i:s') }}";
        const serverTime    = new Date(serverTimeStr).getTime();
        const clientTime    = Date.now();
        const offset        = serverTime - clientTime;

        const NAMA_BULAN = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

        function updateLiveClock() {
            const now = new Date(Date.now() + offset);

            const hh = String(now.getHours()).padStart(2, '0');
            const mm = String(now.getMinutes()).padStart(2, '0');
            const ss = String(now.getSeconds()).padStart(2, '0');

            const tanggal = `${now.getDate()} ${NAMA_BULAN[now.getMonth()]} ${now.getFullYear()}`;

            const clockEl = document.getElementById('liveClock');
            const dateEl  = document.getElementById('liveDate');

            if (clockEl) clockEl.textContent = `${hh}:${mm}:${ss}`;
            if (dateEl)  dateEl.textContent  = tanggal;
        }

        updateLiveClock();
        setInterval(updateLiveClock, 1000);
    })();

    // ============================================================
    // 🔥 AUTO-SYNC TANGGAL — min return_date = borrow_date (boleh sama)
    // ============================================================
    document.addEventListener('DOMContentLoaded', function () {
        const borrowDateInput = document.getElementById('borrowDateInput');
        const returnDateInput = document.getElementById('returnDateInput');

        if (borrowDateInput && returnDateInput) {
            borrowDateInput.addEventListener('change', function () {
                const borrow = this.value;
                if (!borrow) return;

                // 🔥 Min return_date = borrow_date (boleh sama, untuk pinjam beberapa jam)
                returnDateInput.min = borrow;

                // Kalau return_date sekarang < borrow_date, auto-update
                if (returnDateInput.value && returnDateInput.value < borrow) {
                    returnDateInput.value = borrow;
                }
            });
        }
    });
</script>
@endpush