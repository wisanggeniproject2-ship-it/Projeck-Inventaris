<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $unitId = auth()->user()->unit_id;
        
        $query = Item::with('category')->where('unit_id', $unitId);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        $items = $query->latest()->paginate(12);
        
        return view('manager.items.index', compact('items'));
    }

    public function show(Item $item)
    {
        // Pastikan item milik unit manager
        if ($item->unit_id !== auth()->user()->unit_id) {
            abort(403);
        }
        
        $item->load(['category', 'unit', 'circulations' => function($q) {
            $q->latest()->take(10);
        }]);
        
        return view('manager.items.show', compact('item'));
    }
}