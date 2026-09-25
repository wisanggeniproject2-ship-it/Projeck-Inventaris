<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\AssetDisposal;
use App\Models\Unit;
use Illuminate\Http\Request;

class DisposalMonitoringController extends Controller
{
    /**
     * 📋 List semua pengajuan penghapusan aset (READ-ONLY)
     * 
     * Manager cuma bisa lihat, tidak bisa approve/reject.
     */
    public function index(Request $request)
    {
        $query = AssetDisposal::with([
            'item.category', 
            'item.unit', 
            'itemStock', 
            'user', 
            'approver'
        ]);

        // 🔥 Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 🔥 Filter unit
        if ($request->filled('unit')) {
            $query->whereHas('item', function($q) use ($request) {
                $q->where('unit_id', $request->unit);
            });
        }

        // 🔥 Filter tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // 🔥 Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('stock_code', 'like', "%{$search}%")
                  ->orWhere('reason', 'like', "%{$search}%")
                  ->orWhereHas('item', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('full_code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $disposals = $query->latest()->paginate(15);
        $units     = Unit::where('is_active', true)->orderBy('name')->get();

        // 🔥 Statistik
        $stats = [
            'total'     => AssetDisposal::count(),
            'pending'   => AssetDisposal::where('status', 'pending')->count(),
            'approved'  => AssetDisposal::where('status', 'approved')->count(),
            'rejected'  => AssetDisposal::where('status', 'rejected')->count(),
        ];

        return view('manager.disposal-monitoring.index', compact(
            'disposals', 
            'units', 
            'stats'
        ));
    }

    /**
     * 👁️ Detail pengajuan penghapusan (READ-ONLY)
     * 
     * Tampilkan semua info termasuk foto bukti, alasan, riwayat.
     */
    public function show(AssetDisposal $disposal)
    {
        $disposal->load([
            'item.category', 
            'item.unit', 
            'item.fundingSource',
            'itemStock', 
            'user', 
            'approver'
        ]);

        return view('manager.disposal-monitoring.show', compact('disposal'));
    }
}