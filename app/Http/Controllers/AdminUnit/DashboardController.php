<?php

namespace App\Http\Controllers\AdminUnit;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Circulation;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil unit_id dari user yang login
        $unitId = auth()->user()->unit_id;
        
        // Statistik untuk unit tersebut
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
        
        // Barang terbaru di unit tersebut
        $recentItems = Item::with(['category'])
            ->where('unit_id', $unitId)
            ->latest()
            ->take(5)
            ->get();
        
        // Peminjaman terbaru di unit tersebut
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