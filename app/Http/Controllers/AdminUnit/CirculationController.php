<?php

namespace App\Http\Controllers\AdminUnit;

use App\Http\Controllers\Controller;
use App\Models\Circulation;
use App\Models\Item;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class CirculationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $unitId = auth()->user()->unit_id;
        
        // Ambil semua peminjaman untuk unit ini
        $query = Circulation::with(['item', 'user', 'item.unit'])
            ->whereHas('item', function($q) use ($unitId) {
                $q->where('unit_id', $unitId);
            });
        
        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Urutkan: pending, return_pending, approved, returned, rejected
        $circulations = $query->orderByRaw("FIELD(status, 'pending', 'return_pending', 'approved', 'returned', 'rejected')")
            ->latest()
            ->paginate(10);
        
        // Hitung notifikasi pending
        $pendingCount = Circulation::whereHas('item', function($q) use ($unitId) {
                $q->where('unit_id', $unitId);
            })
            ->where('status', 'pending')
            ->count();
        
        // Hitung notifikasi return pending
        $returnPendingCount = Circulation::whereHas('item', function($q) use ($unitId) {
                $q->where('unit_id', $unitId);
            })
            ->where('status', 'return_pending')
            ->count();
        
        return view('admin_unit.circulations.index', compact('circulations', 'pendingCount', 'returnPendingCount'));
    }

    public function show(Circulation $circulation)
    {
        if ($circulation->item->unit_id !== auth()->user()->unit_id) {
            abort(403);
        }
        
        $circulation->load(['item', 'user', 'approver', 'returnConfirmer', 'item.unit']);
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
        
        // UPDATE STATUS BARANG MENJADI BORROWED
        $item = $circulation->item;
        $item->status = 'borrowed';
        $item->save();
        
        // 🔥 KIRIM NOTIFIKASI KE ADMIN UNIT (approved)
        $this->notificationService->sendCirculationNotification($circulation, 'approved');
        
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
        
        // STATUS BARANG TETAP AVAILABLE (TIDAK BERUBAH)
        
        // 🔥 KIRIM NOTIFIKASI KE ADMIN UNIT (rejected)
        $this->notificationService->sendCirculationNotification($circulation, 'rejected');
        
        return back()->with('success', 'Peminjaman berhasil ditolak.');
    }

    // ==================== KONFIRMASI PENGEMBALIAN (BARU) ====================
    public function confirmReturn(Circulation $circulation)
    {
        if ($circulation->item->unit_id !== auth()->user()->unit_id) {
            abort(403);
        }
        
        // Cek apakah status return_pending
        if (!$circulation->isReturnPending()) {
            return back()->with('error', 'Peminjaman ini tidak dalam status menunggu pengembalian.');
        }
        
        // Update status sirkulasi
        $circulation->status = 'returned';
        $circulation->return_date = now();
        $circulation->return_confirmed_by = auth()->id();
        $circulation->return_confirmed_at = now();
        $circulation->save();
        
        // UPDATE STATUS BARANG MENJADI AVAILABLE KEMBALI
        $item = $circulation->item;
        $item->status = 'available';
        $item->save();
        
        // 🔥 KIRIM NOTIFIKASI KE ADMIN UNIT (returned)
        $this->notificationService->sendCirculationNotification($circulation, 'returned');
        
        return back()->with('success', 'Pengembalian barang berhasil dikonfirmasi! Barang sudah tersedia kembali.');
    }
}