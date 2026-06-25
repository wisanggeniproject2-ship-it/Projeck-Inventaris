<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Circulation;
use App\Models\Unit;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $unitId = auth()->user()->unit_id;
        $userId = auth()->id();
        
        // Ambil semua barang tersedia dari semua unit (untuk ditampilkan di dashboard)
        $availableItems = Item::where('status', 'available')
            ->with('unit')
            ->latest()
            ->take(6)
            ->get();
        
        // AMBIL PEMINJAMAN USER (untuk ditampilkan di dashboard)
        $myCirculations = Circulation::where('user_id', $userId)
            ->with(['item', 'item.unit'])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'returned', 'rejected')")
            ->latest()
            ->take(5)
            ->get();
        
        // Hitung statistik
        $stats = [
            'active' => Circulation::where('user_id', $userId)
                ->whereIn('status', ['pending', 'approved'])
                ->count(),
            'pending' => Circulation::where('user_id', $userId)
                ->where('status', 'pending')
                ->count(),
            'approved' => Circulation::where('user_id', $userId)
                ->where('status', 'approved')
                ->count(),
            'returned' => Circulation::where('user_id', $userId)
                ->where('status', 'returned')
                ->count(),
        ];
        
        // Ambil semua unit untuk filter
        $units = Unit::where('is_active', true)->get();
        
        return view('user.dashboard', compact(
            'availableItems', 
            'myCirculations', 
            'stats', 
            'units'
        ));
    }
}