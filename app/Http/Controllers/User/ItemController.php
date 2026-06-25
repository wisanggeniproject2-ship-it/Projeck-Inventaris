<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Unit;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        // Tampilkan SEMUA barang dari SEMUA unit (termasuk yang dipinjam)
        $query = Item::with(['category', 'unit', 'activeCirculation']);
        
        // Filter berdasarkan unit
        if ($request->filled('unit')) {
            $query->where('unit_id', $request->unit);
        }
        
        // Filter berdasarkan status (available/borrowed)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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
        
        return view('user.items.index', compact('items', 'units'));
    }

    public function show(Item $item)
    {
        $item->load(['category', 'unit', 'circulations' => function($q) {
            $q->latest()->take(5);
        }]);
        
        // Cek apakah barang sedang dipinjam
        $isBorrowed = $item->status === 'borrowed';
        $activeCirculation = $item->activeCirculation;
        
        return view('user.items.show', compact('item', 'isBorrowed', 'activeCirculation'));
    }
}