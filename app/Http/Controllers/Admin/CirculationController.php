<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Circulation;
use App\Models\Item;
use Illuminate\Http\Request;

class CirculationController extends Controller
{
    public function __construct()
    {
        // HAPUS: $this->middleware('role:super_admin');
    }

    public function index(Request $request)
    {
        $query = Circulation::with(['item', 'user', 'approver']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $circulations = $query->latest()->paginate(10);
        
        return view('admin.circulations.index', compact('circulations'));
    }

    public function show(Circulation $circulation)
    {
        $circulation->load(['item', 'user', 'approver']);
        return view('admin.circulations.show', compact('circulation'));
    }

  public function approve(Circulation $circulation)
{
    if (!$circulation->isPending()) {
        return back()->with('error', 'Peminjaman ini tidak bisa disetujui.');
    }
    
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
    
    return back()->with('success', 'Peminjaman berhasil disetujui!');
}

public function reject(Circulation $circulation)
{
    if (!$circulation->isPending()) {
        return back()->with('error', 'Peminjaman ini tidak bisa ditolak.');
    }
    
    $circulation->status = 'rejected';
    $circulation->save();
    
    // 🔥 STATUS BARANG TETAP AVAILABLE
    return back()->with('success', 'Peminjaman berhasil ditolak.');
}

public function markReturned(Circulation $circulation)
{
    if (!$circulation->isApproved()) {
        return back()->with('error', 'Hanya peminjaman yang disetujui yang bisa dikembalikan.');
    }
    
    $circulation->status = 'returned';
    $circulation->return_date = now();
    $circulation->save();
    
    // 🔥 UPDATE STATUS BARANG MENJADI AVAILABLE KEMBALI
    $item = $circulation->item;
    $item->status = 'available';
    $item->save();
    
    return back()->with('success', 'Barang berhasil dikembalikan!');
}
}