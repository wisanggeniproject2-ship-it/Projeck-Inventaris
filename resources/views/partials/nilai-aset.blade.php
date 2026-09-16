@php
    // Hanya hitung kalau user super_admin
    $totalNilaiAset = 0;
    $totalJumlahBarang = 0;
    $perKategori = [];

    if (Auth::check() && Auth::user()->role === 'super_admin') {
        $items = \App\Models\Item::with('category')->get();

        foreach ($items as $item) {
            $harga = (float) ($item->price ?? 0);
            $stok  = (int)   ($item->stock ?? 1);
            $subtotal = $harga * $stok;

            $totalNilaiAset    += $subtotal;
            $totalJumlahBarang += $stok;

            $namaKategori = optional($item->category)->name ?? 'Tanpa Kategori';

            if (!isset($perKategori[$namaKategori])) {
                $perKategori[$namaKategori] = ['jumlah' => 0, 'nilai' => 0];
            }

            $perKategori[$namaKategori]['jumlah'] += $stok;
            $perKategori[$namaKategori]['nilai']  += $subtotal;
        }

        // Urutkan dari nilai terbesar
        uasort($perKategori, fn($a, $b) => $b['nilai'] <=> $a['nilai']);
    }
@endphp

@auth
@if(Auth::user()->role === 'super_admin')
<div class="mx-3 mb-3 mt-2 rounded-2xl bg-gradient-to-br from-amber-500/20 via-amber-400/10 to-transparent border border-amber-300/30 overflow-hidden">

    {{-- HEADER --}}
    <div class="flex items-center gap-2 px-4 py-3 border-b border-amber-300/20 bg-amber-500/10">
        <div class="w-8 h-8 rounded-lg bg-amber-400/20 flex items-center justify-center shrink-0">
            <i class="fas fa-coins text-amber-300 text-sm"></i>
        </div>
        <div class="min-w-0">
            <p class="text-white font-bold text-sm leading-tight">Nilai Aset</p>
            <p class="text-amber-200/80 text-[10px] leading-tight">Total kekayaan inventaris</p>
        </div>
    </div>

    {{-- TOTAL NILAI ASET + JUMLAH BARANG --}}
    <div class="px-4 py-3 border-b border-amber-300/10">
        <p class="text-[10px] uppercase tracking-wider text-amber-200/70 mb-1">Total Nilai Aset</p>
        <p class="text-xl font-bold text-white leading-tight">
            Rp {{ number_format($totalNilaiAset, 0, ',', '.') }}
        </p>
        <p class="text-[11px] text-amber-100/80 mt-1">
            <i class="fas fa-boxes-stacked mr-1 text-amber-300"></i>
            {{ number_format($totalJumlahBarang, 0, ',', '.') }} unit barang
        </p>
    </div>

    {{-- PER KATEGORI --}}
    <div class="px-4 py-3 border-b border-amber-300/10">
        <p class="text-[10px] uppercase tracking-wider text-amber-200/70 mb-2">Per Kategori</p>

        @if(empty($perKategori))
            <p class="text-[11px] text-amber-100/60 italic">Belum ada data barang</p>
        @else
            <div class="space-y-2 max-h-48 overflow-y-auto thin-scroll pr-1">
                @foreach($perKategori as $nama => $data)
                <div class="flex items-start justify-between gap-2 text-[11px]">
                    <div class="min-w-0 flex-1">
                        <p class="text-white font-medium truncate" title="{{ $nama }}">{{ $nama }}</p>
                        <p class="text-amber-200/70 text-[10px]">{{ number_format($data['jumlah'], 0, ',', '.') }} unit</p>
                    </div>
                    <p class="text-amber-100 font-semibold whitespace-nowrap text-right">
                        Rp {{ number_format($data['nilai'], 0, ',', '.') }}
                    </p>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- TOTAL DI BAWAH --}}
    <div class="px-4 py-3 bg-amber-500/10">
        <div class="flex items-center justify-between">
            <p class="text-[11px] uppercase tracking-wider text-amber-200/90 font-semibold">Total Keseluruhan</p>
            <p class="text-sm font-bold text-white">Rp {{ number_format($totalNilaiAset, 0, ',', '.') }}</p>
        </div>
    </div>

</div>
@endif
@endauth