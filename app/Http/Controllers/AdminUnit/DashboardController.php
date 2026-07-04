<?php

namespace App\Http\Controllers\AdminUnit;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Circulation;
use App\Models\User;

class DashboardController extends Controller
{
    // HAPUS CONSTRUCTOR

    public function index()
    {
        $unitId = auth()->user()->unit_id;
        
        $stats = [
            'total_items' => Item::where('unit_id', $unitId)->count(),
            'total_borrowed' => Circulation::whereHas('item', function($query) use ($unitId) {
                $query->where('unit_id', $unitId);
            })->where('status', 'approved')->count(),
            'total_pending' => Circulation::whereHas('item', function($query) use ($unitId) {
                $query->where('unit_id', $unitId);
            })->where('status', 'pending')->count(),
            'total_returned' => Circulation::whereHas('item', function($query) use ($unitId) {
                $query->where('unit_id', $unitId);
            })->where('status', 'returned')->count(),
            'total_users' => User::where('unit_id', $unitId)->count(),
        ];
        
        $recentItems = Item::with(['category'])
            ->where('unit_id', $unitId)
            ->latest()
            ->take(5)
            ->get();
        
        $recentCirculations = Circulation::with(['item', 'user'])
            ->whereHas('item', function($query) use ($unitId) {
                $query->where('unit_id', $unitId);
            })
            ->latest()
            ->take(5)
            ->get();
        
        return view('admin_unit.dashboard', compact('stats', 'recentItems', 'recentCirculations'));
    }
}