<?php

namespace App\Services;

use App\Models\Item;

class ItemCodeGenerator
{
    /**
     * Generate full_code untuk sebuah item
     * 
     * Format: {KATEGORI}/{KODE_BARANG}.{LOKASI}.{UNIT}/{NO_URUT}.{NO_STOK}/{TGL}/{BLN_ROMAWI}/{THN}
     * 
     * Contoh: ELC/TVL-001.RUA.DCP/1.01/21/IX/2026
     */
    public function generate(Item $item, int $stockNumber = 1): string
    {
        // 1. Kategori (dari categories.code)
        $kategori = strtoupper($item->category->code ?? 'UNK');

        // 2. Kode barang: {3 huruf nama}-{no urut}
        $noUrut     = $this->getNoUrut($item);
        $kodeBarang = $this->generateKodeBarang($item->name, $noUrut);

        // 3. Lokasi (singkatan otomatis, max 3 huruf)
        $lokasi = $this->singkatLokasi($item->location);

        // 4. Unit (dari units.code)
        $unit = strtoupper($item->unit->code ?? 'UNK');

        // 5. No stok (2 digit, misal 01, 02, ..., 10)
        $noStok = str_pad($stockNumber, 2, '0', STR_PAD_LEFT);

        // 6. Tanggal
        $tanggal = $item->created_at ?? now();
        $tgl     = $tanggal->format('d');
        $bulan   = $this->bulanRomawi($tanggal->month);
        $tahun   = $tanggal->format('Y');

        // Gabung jadi 1 string
        return "{$kategori}/{$kodeBarang}.{$lokasi}.{$unit}/{$noUrut}.{$noStok}/{$tgl}/{$bulan}/{$tahun}";
    }

    /**
     * Get no urut barang per kategori
     * 
     * Contoh: barang ke-1 di kategori Elektronik → 1
     *         barang ke-2 di kategori Elektronik → 2
     *         barang ke-1 di kategori Furniture → 1
     */
    private function getNoUrut(Item $item): int
    {
        $no = Item::where('category_id', $item->category_id)
            ->where('id', '<=', $item->id)
            ->count();

        return max($no, 1);
    }

    /**
     * Generate kode barang: {3 huruf nama}-{no urut 3 digit}
     * 
     * Contoh:
     *   "TV LED 32 Inch"  → "TVL-001"
     *   "Meja Belajar"    → "MB-002" → dipad "MBX-002"
     *   "Projector Epson" → "PE-003" → dipad "PEX-003"
     */
    private function generateKodeBarang(string $nama, int $noUrut): string
    {
        // Ambil huruf pertama tiap kata
        $kata = preg_split('/\s+/', trim($nama));
        $kode = '';

        foreach ($kata as $k) {
            if (!empty($k)) {
                $kode .= strtoupper(substr($k, 0, 1));
            }
        }

        // Kalau kurang dari 3 huruf, ambil dari nama langsung
        if (strlen($kode) < 3) {
            $bersih = preg_replace('/[^A-Za-z]/', '', $nama);
            $kode   = strtoupper(substr($bersih, 0, 3));
        }

        // Max 3 huruf, padding kalau kurang
        $kode = substr($kode, 0, 3);
        $kode = str_pad($kode, 3, 'X', STR_PAD_RIGHT);

        $noUrutPad = str_pad($noUrut, 3, '0', STR_PAD_LEFT);

        return "{$kode}-{$noUrutPad}";
    }

    /**
     * Singkat lokasi jadi max 3 huruf
     * 
     * Contoh:
     *   "Ruang Bermain" → "RUA"
     *   "Gudang A"      → "GUD"
     *   "Ruang Kelas"   → "RUA"
     *   ""              → "UNK"
     */
    private function singkatLokasi(?string $lokasi): string
    {
        if (empty($lokasi)) {
            return 'UNK';
        }

        // Ambil huruf saja (hapus spasi, angka, simbol)
        $bersih = preg_replace('/[^A-Za-z]/', '', $lokasi);

        return strtoupper(substr($bersih, 0, 3));
    }

    /**
     * Konversi bulan (1-12) ke Romawi (I-XII)
     */
    private function bulanRomawi(int $bulan): string
    {
        $romawi = [
            1  => 'I',
            2  => 'II',
            3  => 'III',
            4  => 'IV',
            5  => 'V',
            6  => 'VI',
            7  => 'VII',
            8  => 'VIII',
            9  => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        return $romawi[$bulan] ?? 'I';
    }
}