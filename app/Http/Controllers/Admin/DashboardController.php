<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Circulation;
use App\Models\User;
use App\Models\Unit;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_items' => Item::count(),
            'total_borrowed' => Circulation::where('status', 'approved')->count(),
            'total_pending' => Circulation::where('status', 'pending')->count(),
            'total_units' => Unit::count(),
            'total_users' => User::count(),
        ];
        
        $recentItems = Item::with(['category', 'unit'])->latest()->take(5)->get();
        $recentCirculations = Circulation::with(['item', 'user'])->latest()->take(5)->get();
        
        return view('admin.dashboard', compact('stats', 'recentItems', 'recentCirculations'));
    }
}