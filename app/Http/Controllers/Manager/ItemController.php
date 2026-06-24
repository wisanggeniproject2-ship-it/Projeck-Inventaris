<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Unit;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function __construct()
    {
        // HAPUS: $this->middleware('role:manager');
    }

    public function index(Request $request)
    {
        $query = Item::with(['category', 'unit']);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('unit')) {
            $query->where('unit_id', $request->unit);
        }
        
        $items = $query->latest()->paginate(15);
        $units = Unit::where('is_active', true)->get();
        
        return view('manager.items.index', compact('items', 'units'));
    }

    public function show(Item $item)
    {
        $item->load(['category', 'unit', 'circulations.user']);
        return view('manager.items.show', compact('item'));
    }
}