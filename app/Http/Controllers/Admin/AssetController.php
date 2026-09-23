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
     * 🔥 Halaman Nilai Aset (existing)
     */
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
     * DIKELOMPOKKAN PER UNIT — bukan cuma tabel rata
     */
    public function depreciation(Request $request)
    {
        // Base query: hanya barang yang punya harga & tanggal beli
        $query = Item::with(['category', 'unit', 'fundingSource'])
            ->whereNotNull('price')
            ->whereNotNull('purchase_date')
            ->where('price', '>', 0);

        // Filter kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter unit (kalau pilih 1 unit, tampil cuma unit itu)
        if ($request->filled('unit')) {
            $query->where('unit_id', $request->unit);
        }

        // Ambil SEMUA data (bukan paginate) — nanti dikelompokkan per unit
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
                $stok = (int) $item->stock;

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

        // 🔥 Grand total (semua unit digabung)
        $grandTotalNilai  = collect($unitSummary)->sum('total_nilai');
        $grandTotalAkum   = collect($unitSummary)->sum('total_akumulasi');
        $grandTotalBuku   = collect($unitSummary)->sum('total_buku');
        $grandTotalBarang = collect($unitSummary)->sum('total_barang');

        // Untuk filter dropdown
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