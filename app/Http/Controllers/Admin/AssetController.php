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
     * 
     * 🔥 CATATAN:
     * - Total & perKategori → dihitung dari SEMUA item (TIDAK ikut filter)
     * - Daftar items        → IKUT filter + paginate 15
     */
    public function index(Request $request)
    {
        // ============================================================
        // 1. HITUNG TOTAL & PER KATEGORI (DARI SEMUA ITEM — TIDAK DIFILTER)
        // ============================================================
        $allItems = Item::with(['category', 'stockCodes'])->get();

        $totalNilaiAset    = 0.0;
        $totalJumlahBarang = 0;
        $perKategori       = [];

        foreach ($allItems as $item) {
            $harga = (float) $item->price;
            $stok  = $item->stock_for_asset;

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

        // ============================================================
        // 2. DAFTAR ITEMS — IKUT FILTER + PAGINATE 15
        // ============================================================
        $itemsQuery = Item::with(['category', 'unit', 'fundingSource', 'stockCodes']);

        // Filter kategori
        if ($request->filled('category')) {
            $itemsQuery->where('category_id', $request->category);
        }

        // Filter unit
        if ($request->filled('unit')) {
            $itemsQuery->where('unit_id', $request->unit);
        }

        // Filter kondisi
        if ($request->filled('condition')) {
            $itemsQuery->where('condition', $request->condition);
        }

        // Search nama / kode
        if ($request->filled('search')) {
            $search = $request->search;
            $itemsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $items = $itemsQuery->latest()->paginate(15)->withQueryString();

        $categories = Category::orderBy('name')->get();
        $units      = Unit::where('is_active', true)->orderBy('name')->get();

        return view('admin.assets.index', compact(
            'totalNilaiAset',
            'totalJumlahBarang',
            'perKategori',
            'items',
            'categories',
            'units'
        ));
    }

    /**
     * 🔥 Halaman Penyusutan Aset
     * DIKELOMPOKKAN PER UNIT
     * 
     * Perhitungan pakai stock_for_asset (Ready + Dipinjam + Maintenance)
     * Disposed tidak dihitung
     * 
     * 🔥 CATATAN:
     * - Grand total & unit summary → dihitung dari SEMUA item (TIDAK ikut filter)
     * - Daftar items per unit      → IKUT filter + paginate 15
     */
    public function depreciation(Request $request)
    {
        // ============================================================
        // 1. HITUNG GRAND TOTAL & UNIT SUMMARY (DARI SEMUA — TIDAK DIFILTER)
        // ============================================================
        $allItems = Item::with(['category', 'unit', 'fundingSource', 'stockCodes'])
            ->whereNotNull('price')
            ->whereNotNull('purchase_date')
            ->where('price', '>', 0)
            ->orderBy('unit_id', 'asc')
            ->orderBy('purchase_date', 'asc')
            ->get();

        $itemsByUnit = $allItems->groupBy(function ($item) {
            return $item->unit->name ?? 'Tanpa Unit';
        });

        $unitSummary = [];
        foreach ($itemsByUnit as $unitName => $unitItems) {
            $totalNilaiAset = 0.0;
            $totalAkumulasi = 0.0;
            $totalNilaiBuku = 0.0;
            $totalBarang    = 0;

            foreach ($unitItems as $item) {
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

        $grandTotalNilai  = collect($unitSummary)->sum('total_nilai');
        $grandTotalAkum   = collect($unitSummary)->sum('total_akumulasi');
        $grandTotalBuku   = collect($unitSummary)->sum('total_buku');
        $grandTotalBarang = collect($unitSummary)->sum('total_barang');

        // ============================================================
        // 2. DAFTAR ITEMS — IKUT FILTER + PAGINATE 15
        // ============================================================
        $itemsQuery = Item::with(['category', 'unit', 'fundingSource', 'stockCodes'])
            ->whereNotNull('price')
            ->whereNotNull('purchase_date')
            ->where('price', '>', 0);

        // Filter kategori
        if ($request->filled('category')) {
            $itemsQuery->where('category_id', $request->category);
        }

        // Filter unit
        if ($request->filled('unit')) {
            $itemsQuery->where('unit_id', $request->unit);
        }

        // Search nama / kode
        if ($request->filled('search')) {
            $search = $request->search;
            $itemsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $filteredItems = $itemsQuery
            ->orderBy('unit_id', 'asc')
            ->orderBy('purchase_date', 'asc')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();
        $units      = Unit::where('is_active', true)->orderBy('name')->get();

        return view('admin.assets.depreciation', compact(
            'unitSummary',
            'categories',
            'units',
            'grandTotalNilai',
            'grandTotalAkum',
            'grandTotalBuku',
            'grandTotalBarang',
            'filteredItems'
        ));
    }
}