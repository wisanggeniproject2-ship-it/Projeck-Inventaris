<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Circulation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $unitId = auth()->user()->unit_id;
        
        // Available items for borrowing
        $availableItems = Item::where('unit_id', $unitId)
            ->where('status', 'available')
            ->latest()
            ->take(6)
            ->get();
        
        // Active loans count
        $activeLoans = Circulation::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'approved'])
            ->count();
        
        // Recent circulations
        $recentCirculations = Circulation::where('user_id', auth()->id())
            ->with('item')
            ->latest()
            ->take(5)
            ->get();
        
        return view('user.dashboard', compact('availableItems', 'activeLoans', 'recentCirculations'));
    }
}