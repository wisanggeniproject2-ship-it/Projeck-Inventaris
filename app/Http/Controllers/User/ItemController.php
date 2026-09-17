<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Unit;
use App\Models\Category;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['category', 'unit', 'activeCirculation']);
        
        // Filter unit
        if ($request->filled('unit')) {
            $query->where('unit_id', $request->unit);
        }
        
        // Filter kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        // Filter status
        if ($request->filled('status')) {
            if ($request->status == 'available') {
                $query->where('status', 'available')->where('condition', 'baik');
            } elseif ($request->status == 'unavailable') {
                $query->where(function($q) {
                    $q->where('status', 'borrowed')
                      ->orWhere('condition', 'rusak')
                      ->orWhere('condition', 'perbaikan');
                });
            }
        }
        
        // Filter "Pinjamanku"
        if ($request->filled('filter') && $request->filter == 'my') {
            $myItemIds = \App\Models\Circulation::where('user_id', auth()->id())
                ->whereIn('status', ['approved', 'pending'])
                ->pluck('item_id');
            $query->whereIn('id', $myItemIds);
        }
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        $items = $query->latest()->paginate(12);
        $units = Unit::where('is_active', true)->get();
        $categories = Category::all();
        
        return view('user.items.index', compact('items', 'units', 'categories'));
    }

    public function show(Item $item)
    {
        $item->load(['category', 'unit', 'circulations' => function($q) {
            $q->latest()->take(5);
        }]);
        
        $isBorrowed = $item->status === 'borrowed';
        $activeCirculation = $item->activeCirculation;
        $canBorrow = $item->canBeBorrowed();
        
        return view('user.items.show', compact('item', 'isBorrowed', 'activeCirculation', 'canBorrow'));
    }

    /**
     * 🔥 BARU: Ambil daftar unit yang punya barang ini
     *
     * Konsep:
     * - User mau pinjam "Mic"
     * - Cek semua unit: apakah punya barang dengan NAMA SAMA?
     * - Kalau iya & stok > 0 → bisa dipilih
     * - Kalau stok 0 / rusak / tidak ada → disabled (abu-abu)
     */
    public function getUnitsForItem(Item $item)
    {
        // Ambil semua barang dengan NAMA SAMA (barang yang sama di unit berbeda)
        $siblingItems = Item::with('unit')
            ->where('name', $item->name)
            ->whereNotNull('unit_id')
            ->get()
            ->keyBy('unit_id');

        // Ambil semua unit yang aktif
        $units = Unit::where('is_active', true)->orderBy('name')->get();

        // Gabungkan: tiap unit, cek apakah punya item dengan nama sama
        $unitsData = $units->map(function ($unit) use ($siblingItems) {
            $sibling = $siblingItems->get($unit->id);

            // Kalau unit tidak punya barang ini
            if (!$sibling) {
                return [
                    'id'         => $unit->id,
                    'name'       => $unit->name,
                    'item_id'    => null,
                    'stock'      => 0,
                    'can_borrow' => false,
                    'reason'     => 'Barang tidak tersedia di unit ini',
                ];
            }

            // Cek apakah bisa dipinjam
            $canBorrow = $sibling->stock > 0
                      && $sibling->status === 'available'
                      && $sibling->condition === 'baik';

            // Tentukan alasan kalau tidak bisa dipinjam
            if ($canBorrow) {
                $reason = null;
            } elseif ($sibling->stock <= 0) {
                $reason = 'Stok habis';
            } elseif ($sibling->condition === 'rusak') {
                $reason = 'Barang rusak';
            } elseif ($sibling->condition === 'perbaikan') {
                $reason = 'Dalam perbaikan';
            } elseif ($sibling->status === 'borrowed') {
                $reason = 'Sedang dipinjam';
            } else {
                $reason = 'Tidak tersedia';
            }

            return [
                'id'         => $unit->id,
                'name'       => $unit->name,
                'item_id'    => $sibling->id,
                'stock'      => (int) $sibling->stock,
                'can_borrow' => $canBorrow,
                'reason'     => $reason,
            ];
        });

        // Urutkan: yang bisa dipinjam di atas, yang tidak bisa di bawah
        $unitsData = $unitsData->sortByDesc('can_borrow')->values();

        return response()->json([
            'item'  => [
                'id'   => $item->id,
                'name' => $item->name,
                'code' => $item->code,
            ],
            'units' => $unitsData,
        ]);
    }
}