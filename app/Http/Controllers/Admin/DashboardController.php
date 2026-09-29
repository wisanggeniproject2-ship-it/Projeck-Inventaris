<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Circulation;
use App\Models\User;
use App\Models\Unit;
use App\Models\AssetDisposal;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

        // ============================================================
        // 🔥 BATAS 7 HARI TERAKHIR
        // ============================================================
        $sevenDaysAgo = Carbon::now()->subDays(7);

        // 🔥 Barang terbaru — 7 hari terakhir
        $recentItems = Item::with(['category', 'unit'])
            ->where('created_at', '>=', $sevenDaysAgo)
            ->latest()
            ->take(5)
            ->get();

        // 🔥 Peminjaman terbaru — 7 hari terakhir
        $recentCirculations = Circulation::with(['item', 'user'])
            ->where('created_at', '>=', $sevenDaysAgo)
            ->latest()
            ->take(5)
            ->get();

        // 🔥 Pengajuan penghapusan terbaru — 7 hari terakhir
        $recentDisposals = AssetDisposal::with(['item', 'user'])
            ->where('created_at', '>=', $sevenDaysAgo)
            ->latest()
            ->take(5)
            ->get();

        // 🔥 Total pengajuan yang masih pending (SEMUA, tidak dibatasi 7 hari)
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
            'stats',
            'recentItems',
            'recentCirculations',
            'recentDisposals',
            'chartTrend',
            'unitBreakdown'
        ));
    }
}