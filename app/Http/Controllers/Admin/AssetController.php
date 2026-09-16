<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;

class AssetController extends Controller
{
    public function index()
    {
        // Ambil semua barang + relasi kategori & unit
        $items = Item::with(['category', 'unit'])->get();

        // Inisialisasi sebagai float/int (BUKAN null/string)
        $totalNilaiAset    = 0.0;
        $totalJumlahBarang = 0;
        $perKategori       = [];

        foreach ($items as $item) {
            // 🔥 CAST WAJIB — karena cast 'decimal:2' di model mengembalikan STRING
            $harga    = (float) $item->price;
            $stok     = (int)   $item->stock;
            $subtotal = $harga * $stok;

            // Akumulasi dengan cast biar konsisten
            $totalNilaiAset    = (float) $totalNilaiAset + (float) $subtotal;
            $totalJumlahBarang = (int)   $totalJumlahBarang + (int) $stok;

            $namaKategori = optional($item->category)->name ?? 'Tanpa Kategori';

            if (!isset($perKategori[$namaKategori])) {
                $perKategori[$namaKategori] = [
                    'nama'   => $namaKategori,
                    'jumlah' => 0,
                    'nilai'  => 0.0,
                    'items'  => [],
                ];
            }

            $perKategori[$namaKategori]['jumlah'] = (int)   $perKategori[$namaKategori]['jumlah'] + (int) $stok;
            $perKategori[$namaKategori]['nilai']  = (float) $perKategori[$namaKategori]['nilai']  + (float) $subtotal;
            $perKategori[$namaKategori]['items'][] = $item;
        }

        // Urutkan dari nilai terbesar (mempertahankan key string = nama kategori)
        uasort($perKategori, fn($a, $b) => $b['nilai'] <=> $a['nilai']);

        // 🔥 KUNCI: Reset key jadi 0, 1, 2, ... (integer)
        // Tanpa ini, key masih nama kategori (string) → di view `$i + 1` error
        $perKategori = array_values($perKategori);

        // Hitung persentase per kategori
        foreach ($perKategori as &$kat) {
            $kat['persen'] = $totalNilaiAset > 0
                ? round(((float) $kat['nilai'] / (float) $totalNilaiAset) * 100, 1)
                : 0;
        }
        unset($kat);

        return view('admin.assets.index', compact(
            'totalNilaiAset',
            'totalJumlahBarang',
            'perKategori',
            'items'
        ));
    }
}