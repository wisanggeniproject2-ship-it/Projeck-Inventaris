<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Unit;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        // Ambil data untuk landing page
        $totalItems = Item::count();
        $totalUnits = Unit::count();
        $availableItems = Item::where('status', 'available')->count();
        
        // Ambil beberapa barang untuk ditampilkan
        $items = Item::with(['category', 'unit'])
            ->latest()
            ->take(6)
            ->get();
        
        return view('landing', compact('totalItems', 'totalUnits', 'availableItems', 'items'));
    }
}