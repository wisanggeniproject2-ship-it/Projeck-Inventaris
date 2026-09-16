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
        
        // 🔥 CEK STOK BARANG
        $item = $circulation->item;
        if ($item->stock <= 0) {
            return back()->with('error', 'Stok barang habis! Tidak bisa menyetujui peminjaman.');
        }
        
        // 🔥 KURANGI STOK
        $item->decreaseStock(1);
        
        // Update status sirkulasi
        $circulation->status = 'approved';
        $circulation->approved_by = auth()->id();
        $circulation->approved_at = now();
        $circulation->save();
        
        return back()->with('success', 'Peminjaman berhasil disetujui! Stok tersisa: ' . $item->stock);
    }

    /**
     * 🔥 REJECT — dengan alasan penolakan
     */
    public function reject(Request $request, Circulation $circulation)
    {
        if (!$circulation->isPending()) {
            return back()->with('error', 'Peminjaman ini tidak bisa ditolak.');
        }

        // Validasi input alasan
        $request->validate([
            'rejection_reason' => 'required|string|min:5|max:500',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            'rejection_reason.min'      => 'Alasan minimal 5 karakter.',
            'rejection_reason.max'      => 'Alasan maksimal 500 karakter.',
        ]);

        // Update status + simpan alasan + waktu
        $circulation->status           = 'rejected';
        $circulation->rejection_reason = $request->rejection_reason;
        $circulation->rejected_at      = now();
        $circulation->save();
        
        // 🔥 STATUS BARANG TETAP AVAILABLE
        return back()->with('success', 'Peminjaman berhasil ditolak.');
    }

    public function markReturned(Circulation $circulation)
    {
        if (!$circulation->isApproved()) {
            return back()->with('error', 'Hanya peminjaman yang disetujui yang bisa dikembalikan.');
        }
        
        // 🔥 TAMBAHKAN STOK KEMBALI
        $item = $circulation->item;
        $item->increaseStock(1);
        
        // Update status sirkulasi
        $circulation->status = 'returned';
        $circulation->return_date = now();
        $circulation->save();
        
        return back()->with('success', 'Barang berhasil dikembalikan! Stok sekarang: ' . $item->stock);
    }
}