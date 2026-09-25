<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * 🔥 Halaman Nilai Aset
     * 
     * KONSEP:
     * - Barang DIPINJAM → tetap milik yayasan → DIHITUNG ✅
     * - Barang MAINTENANCE → masih ada fisiknya → DIHITUNG ✅
     * - Barang DISPOSED → sudah hilang/rusak → TIDAK DIHITUNG ❌
     * 
     * Rumus: Stok Nilai Aset = Ready + Dipinjam + Maintenance
     *                          = count(item_stocks WHERE status IN ('available','borrowed','maintenance'))
     */
    public function index()
    {
        // 🔥 Eager load stockCodes (biar accessor cepat)
        $items = Item::with(['category', 'unit', 'stockCodes'])->get();

        $totalNilaiAset    = 0.0;
        $totalJumlahBarang = 0;
        $perKategori       = [];

        foreach ($items as $item) {
            $harga = (float) $item->price;
            
            // 🔥🔥🔥 STOK UNTUK NILAI ASET
            // = Ready + Dipinjam + Maintenance
            // Disposed dianggap hilang musnah, TIDAK dihitung
            $stok = $item->stock_for_asset;
            
            $subtotal = $harga * $stok;

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

        uasort($perKategori, fn($a, $b) => $b['nilai'] <=> $a['nilai']);
        $perKategori = array_values($perKategori);

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

    /**
     * 🔥 Halaman Penyusutan Aset
     * DIKELOMPOKKAN PER UNIT
     * 
     * Perhitungan pakai stock_for_asset (Ready + Dipinjam + Maintenance)
     * Disposed tidak dihitung
     */
    public function depreciation(Request $request)
    {
        // 🔥 Eager load stockCodes
        $query = Item::with(['category', 'unit', 'fundingSource', 'stockCodes'])
            ->whereNotNull('price')
            ->whereNotNull('purchase_date')
            ->where('price', '>', 0);

        // Filter kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter unit
        if ($request->filled('unit')) {
            $query->where('unit_id', $request->unit);
        }

        // Ambil SEMUA data
        $items = $query->orderBy('unit_id', 'asc')
                       ->orderBy('purchase_date', 'asc')
                       ->get();

        // 🔥 KELOMPOKKAN PER UNIT
        $itemsByUnit = $items->groupBy(function ($item) {
            return $item->unit->name ?? 'Tanpa Unit';
        });

        // 🔥 Hitung total PER UNIT
        $unitSummary = [];
        foreach ($itemsByUnit as $unitName => $unitItems) {
            $totalNilaiAset = 0.0;
            $totalAkumulasi = 0.0;
            $totalNilaiBuku = 0.0;
            $totalBarang    = 0;

            foreach ($unitItems as $item) {
                // 🔥🔥🔥 STOK UNTUK PENYUSUTAN
                // = Ready + Dipinjam + Maintenance
                $stok = $item->stock_for_asset;

                $totalNilaiAset += ((float) $item->price) * $stok;
                $totalAkumulasi += ((float) $item->getAccumulatedDepreciation()) * $stok;
                $totalNilaiBuku += ((float) $item->getBookValue()) * $stok;
                $totalBarang    += $stok;
            }

            $unitSummary[$unitName] = [
                'items'           => $unitItems,
                'total_nilai'     => $totalNilaiAset,
                'total_akumulasi' => $totalAkumulasi,
                'total_buku'      => $totalNilaiBuku,
                'total_barang'    => $totalBarang,
                'jumlah_item'     => $unitItems->count(),
            ];
        }

        // 🔥 Grand total
        $grandTotalNilai  = collect($unitSummary)->sum('total_nilai');
        $grandTotalAkum   = collect($unitSummary)->sum('total_akumulasi');
        $grandTotalBuku   = collect($unitSummary)->sum('total_buku');
        $grandTotalBarang = collect($unitSummary)->sum('total_barang');

        $categories = Category::orderBy('name')->get();
        $units      = Unit::where('is_active', true)->orderBy('name')->get();

        return view('admin.assets.depreciation', compact(
            'unitSummary',
            'categories',
            'units',
            'grandTotalNilai',
            'grandTotalAkum',
            'grandTotalBuku',
            'grandTotalBarang'
        ));
    }
}