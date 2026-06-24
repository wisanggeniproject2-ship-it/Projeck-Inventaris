<?php

namespace App\Http\Controllers\AdminUnit;

use App\Http\Controllers\Controller;
use App\Models\Circulation;
use Illuminate\Http\Request;

class CirculationController extends Controller
{
    public function __construct()
    {
        // HAPUS: $this->middleware('role:admin_unit');
    }

    public function index(Request $request)
    {
        $unitId = auth()->user()->unit_id;
        
        $query = Circulation::with(['item', 'user'])
            ->whereHas('item', function($q) use ($unitId) {
                $q->where('unit_id', $unitId);
            });
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $circulations = $query->latest()->paginate(10);
        
        return view('admin_unit.circulations.index', compact('circulations'));
    }

    public function show(Circulation $circulation)
    {
        if ($circulation->item->unit_id !== auth()->user()->unit_id) {
            abort(403);
        }
        
        $circulation->load(['item', 'user', 'approver']);
        return view('admin_unit.circulations.show', compact('circulation'));
    }

    public function approve(Circulation $circulation)
    {
        if ($circulation->item->unit_id !== auth()->user()->unit_id) {
            abort(403);
        }
        
        if (!$circulation->isPending()) {
            return back()->with('error', 'Peminjaman ini tidak bisa disetujui.');
        }
        
        $circulation->approve();
        $circulation->approved_by = auth()->id();
        $circulation->save();
        
        return back()->with('success', 'Peminjaman berhasil disetujui.');
    }

    public function reject(Circulation $circulation)
    {
        if ($circulation->item->unit_id !== auth()->user()->unit_id) {
            abort(403);
        }
        
        if (!$circulation->isPending()) {
            return back()->with('error', 'Peminjaman ini tidak bisa ditolak.');
        }
        
        $circulation->reject();
        $circulation->save();
        
        return back()->with('success', 'Peminjaman berhasil ditolak.');
    }

    public function markReturned(Circulation $circulation)
    {
        if ($circulation->item->unit_id !== auth()->user()->unit_id) {
            abort(403);
        }
        
        if (!$circulation->isApproved()) {
            return back()->with('error', 'Hanya peminjaman yang disetujui yang bisa dikembalikan.');
        }
        
        $circulation->markAsReturned();
        $circulation->save();
        
        return back()->with('success', 'Barang berhasil dikembalikan.');
    }
}