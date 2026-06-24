<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Circulation;
use App\Models\Unit;

class DashboardController extends Controller
{
    public function __construct()
    {
        // HAPUS: $this->middleware('role:manager');
    }

    public function index()
    {
        $stats = [
            'total_items' => Item::count(),
            'total_borrowed' => Circulation::where('status', 'approved')->count(),
            'total_pending' => Circulation::where('status', 'pending')->count(),
            'total_units' => Unit::count(),
        ];
        
        $recentItems = Item::with(['category', 'unit'])->latest()->take(10)->get();
        $recentCirculations = Circulation::with(['item', 'user'])->latest()->take(10)->get();
        
        $itemsByUnit = Unit::withCount('items')->get();
        
        return view('manager.dashboard', compact('stats', 'recentItems', 'recentCirculations', 'itemsByUnit'));
    }
}