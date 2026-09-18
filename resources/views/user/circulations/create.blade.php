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
                        <i class="fas fa-boxes text-[9px]"></i>Stok: {{ $selectedItem->stock }}
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

                {{-- Tanggal Kembali --}}
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">
                        <i class="fas fa-calendar-alt text-brand-500 mr-1.5"></i>
                        Tanggal Kembali (Estimasi) <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="expected_return_date" 
                           value="{{ old('expected_return_date') }}" 
                           required
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 text-sm transition">
                    @error('expected_return_date') 
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>{{ $message }}
                        </p> 
                    @enderror
                    <p class="text-xs text-gray-400 mt-1.5">
                        <i class="fas fa-info-circle mr-1"></i>
                        Pilih tanggal minimal besok
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