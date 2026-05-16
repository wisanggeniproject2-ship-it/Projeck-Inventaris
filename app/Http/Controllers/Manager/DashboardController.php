<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Circulation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $unitId = auth()->user()->unit_id;
        
        $stats = [
            'total_items' => Item::where('unit_id', $unitId)->count(),
            'total_borrowed' => Circulation::whereHas('item', function($q) use ($unitId) {
                $q->where('unit_id', $unitId);
            })->where('status', 'approved')->count(),
            'total_pending' => Circulation::whereHas('item', function($q) use ($unitId) {
                $q->where('unit_id', $unitId);
            })->where('status', 'pending')->count(),
        ];
        
        $recentItems = Item::with('category')->where('unit_id', $unitId)->latest()->take(5)->get();
        $recentCirculations = Circulation::with('item')->whereHas('item', function($q) use ($unitId) {
            $q->where('unit_id', $unitId);
        })->latest()->take(5)->get();
        
        return view('manager.dashboard', compact('stats', 'recentItems', 'recentCirculations'));
    }
}