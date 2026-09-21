<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Circulation;
use App\Models\User;
use App\Models\Unit;
use App\Models\AssetDisposal;   // 🔥 TAMBAH INI
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
            'total_maintenance' => Item::where('status', 'maintenance')->count(),
        ];

        $recentItems = Item::with(['category', 'unit'])->latest()->take(5)->get();
        $recentCirculations = Circulation::with(['item', 'user'])->latest()->take(5)->get();

        // 🔥 TAMBAH INI — pengajuan penghapusan terbaru
        $recentDisposals = AssetDisposal::with(['item', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // 🔥 TAMBAH INI — hitung total pengajuan yang masih pending
        $stats['total_disposal_pending'] = AssetDisposal::where('status', 'pending')->count();

        // Tren peminjaman 6 bulan terakhir (berdasarkan tanggal pinjam)
        $chartTrend = [
            'labels' => collect(range(5, 0))->map(
                fn ($i) => now()->subMonths($i)->translatedFormat('M')
            )->toArray(),
            'data' => collect(range(5, 0))->map(function ($i) {
                $month = now()->subMonths($i);
                return Circulation::whereMonth('borrow_date', $month->month)
                    ->whereYear('borrow_date', $month->year)
                    ->count();
            })->toArray(),
        ];

        // Ringkasan jumlah barang per unit
        $unitBreakdown = Unit::withCount('items')
            ->orderByDesc('items_count')
            ->get()
            ->map(fn ($unit) => [
                'name' => $unit->name,
                'count' => $unit->items_count,
            ])
            ->toArray();

        return view('admin.dashboard', compact(
            'stats', 'recentItems', 'recentCirculations', 'chartTrend', 'unitBreakdown',
            'recentDisposals'   // 🔥 TAMBAHAN
        ));
    }
}