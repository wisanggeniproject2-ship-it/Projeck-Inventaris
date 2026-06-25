<?php

namespace App\Http\Controllers\AdminUnit;

use App\Http\Controllers\Controller;
use App\Models\Circulation;
use App\Models\Item;
use Illuminate\Http\Request;

class CirculationController extends Controller
{
    public function index(Request $request)
    {
        $unitId = auth()->user()->unit_id;
        
        // Ambil semua peminjaman untuk unit ini (termasuk dari user unit lain)
        $query = Circulation::with(['item', 'user', 'item.unit'])
            ->whereHas('item', function($q) use ($unitId) {
                $q->where('unit_id', $unitId);
            });
        
        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Urutkan: pending dulu, lalu yang lain
        $circulations = $query->orderByRaw("FIELD(status, 'pending', 'approved', 'returned', 'rejected')")
            ->latest()
            ->paginate(10);
        
        // Hitung notifikasi pending
        $pendingCount = Circulation::whereHas('item', function($q) use ($unitId) {
                $q->where('unit_id', $unitId);
            })
            ->where('status', 'pending')
            ->count();
        
        return view('admin_unit.circulations.index', compact('circulations', 'pendingCount'));
    }

    public function show(Circulation $circulation)
    {
        if ($circulation->item->unit_id !== auth()->user()->unit_id) {
            abort(403);
        }
        
        $circulation->load(['item', 'user', 'approver', 'item.unit']);
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
    
    // Cek apakah barang tersedia
    if ($circulation->item->status !== 'available') {
        return back()->with('error', 'Barang sedang tidak tersedia.');
    }
    
    // Update status sirkulasi
    $circulation->status = 'approved';
    $circulation->approved_by = auth()->id();
    $circulation->approved_at = now();
    $circulation->save();
    
    // 🔥 UPDATE STATUS BARANG MENJADI BORROWED
    $item = $circulation->item;
    $item->status = 'borrowed';
    $item->save();
    
    return back()->with('success', 'Peminjaman berhasil disetujui! Status barang telah diupdate.');
}

    public function reject(Circulation $circulation)
{
    if ($circulation->item->unit_id !== auth()->user()->unit_id) {
        abort(403);
    }
    
    if (!$circulation->isPending()) {
        return back()->with('error', 'Peminjaman ini tidak bisa ditolak.');
    }
    
    // Update status sirkulasi
    $circulation->status = 'rejected';
    $circulation->save();
    
    // 🔥 STATUS BARANG TETAP AVAILABLE (TIDAK BERUBAH)
    // Tidak perlu update status item karena barang tetap tersedia
    
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
    
    // Update status sirkulasi
    $circulation->status = 'returned';
    $circulation->return_date = now();
    $circulation->save();
    
    // 🔥 UPDATE STATUS BARANG MENJADI AVAILABLE KEMBALI
    $item = $circulation->item;
    $item->status = 'available';
    $item->save();
    
    return back()->with('success', 'Barang berhasil dikembalikan! Status barang telah diupdate.');
}
}