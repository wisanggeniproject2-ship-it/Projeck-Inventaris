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
}