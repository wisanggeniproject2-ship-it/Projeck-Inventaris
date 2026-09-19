<?php

namespace App\Http\Controllers\Admin;

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
        // Middleware sudah di-handle di routes/web.php (role:super_admin)
    }

    /**
     * 📋 DAFTAR SIRKULASI
     */
    public function index(Request $request)
    {
        $query = Circulation::with(['item', 'user', 'approver']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $circulations = $query->latest()->paginate(10);
        
        return view('admin.circulations.index', compact('circulations'));
    }

    /**
     * 👁️ DETAIL SIRKULASI
     */
    public function show(Circulation $circulation)
    {
        $circulation->load(['item', 'user', 'approver']);
        return view('admin.circulations.show', compact('circulation'));
    }

    /**
     * ✅ APPROVE — setujui peminjaman (pending → approved)
     */
    public function approve(Circulation $circulation)
    {
        if (!$circulation->isPending()) {
            return back()->with('error', 'Peminjaman ini tidak bisa disetujui.');
        }
        
        // 🔥 CEK STOK BARANG
        $item = $circulation->item;
        if (!$item) {
            return back()->with('error', 'Barang tidak ditemukan.');
        }
        
        if ($item->stock <= 0) {
            return back()->with('error', 'Stok barang habis! Tidak bisa menyetujui peminjaman.');
        }
        
        // 🔥 KURANGI STOK
        $item->decreaseStock(1);
        
        // Update status sirkulasi
        $circulation->status      = 'approved';
        $circulation->approved_by = auth()->id();
        $circulation->approved_at = now();
        $circulation->save();

        // 🔔 Notifikasi ke user
        if (method_exists($this->notificationService, 'sendCirculationNotification')) {
            $this->notificationService->sendCirculationNotification($circulation, 'approved');
        }
        
        return back()->with('success', 'Peminjaman berhasil disetujui! Stok tersisa: ' . $item->fresh()->stock);
    }

    /**
     * ❌ REJECT — tolak peminjaman dengan alasan (pending → rejected)
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

        // 🔔 Notifikasi ke user
        if (method_exists($this->notificationService, 'sendCirculationNotification')) {
            $this->notificationService->sendCirculationNotification($circulation, 'rejected');
        }
        
        return back()->with('success', 'Peminjaman berhasil ditolak.');
    }

    /**
     * 📦 MARK RETURNED — langsung tandai barang kembali (approved → returned)
     * 
     * ⚠️ Ini untuk SKENARIO: admin lihat barang sudah dibawa user kembali
     *    dan langsung tandai. Tidak perlu user klik "Ajukan Pengembalian" dulu.
     */
    public function markReturned(Circulation $circulation)
    {
        if (!$circulation->isApproved()) {
            return back()->with('error', 'Hanya peminjaman yang disetujui yang bisa dikembalikan.');
        }
        
        // 🔥 TAMBAHKAN STOK KEMBALI
        $item = $circulation->item;
        if (!$item) {
            return back()->with('error', 'Barang tidak ditemukan.');
        }
        
        $item->increaseStock(1);
        
        // Update status sirkulasi
        $circulation->status              = 'returned';
        $circulation->return_date         = now();
        $circulation->return_confirmed_by = auth()->id();
        $circulation->return_confirmed_at = now();
        $circulation->save();

        // 🔔 Notifikasi ke user
        if (method_exists($this->notificationService, 'sendCirculationNotification')) {
            $this->notificationService->sendCirculationNotification($circulation, 'returned');
        }
        
        return back()->with('success', 'Barang berhasil dikembalikan! Stok sekarang: ' . $item->fresh()->stock);
    }

    /**
     * 🔥🔥🔥 CONFIRM RETURN — konfirmasi pengembalian (return_pending → returned)
     * 
     * ⚠️ INI YANG HILANG SEBELUMNYA!
     * 
     * Alur:
     *   1. User klik "Ajukan Pengembalian" → status jadi 'return_pending'
     *   2. Admin/super_admin klik "Konfirmasi Pengembalian" (method INI) → status jadi 'returned'
     *   3. Stok barang kembali bertambah
     */
    public function confirmReturn(Circulation $circulation)
    {
        // Hanya bisa konfirmasi kalau status return_pending
        if ($circulation->status !== 'return_pending') {
            return back()->with('error', 'Hanya pengembalian yang menunggu konfirmasi yang bisa diproses.');
        }

        $item = $circulation->item;
        if (!$item) {
            return back()->with('error', 'Barang tidak ditemukan.');
        }

        // 🔥 TAMBAHKAN STOK KEMBALI
        $item->increaseStock(1);

        // 🔥 Update status + isi return_date & return_confirmed_at
        $circulation->status              = 'returned';
        $circulation->return_date         = now();
        $circulation->return_confirmed_by = auth()->id();
        $circulation->return_confirmed_at = now();
        $circulation->save();

        // 🔔 Notifikasi ke user
        if (method_exists($this->notificationService, 'sendCirculationNotification')) {
            $this->notificationService->sendCirculationNotification($circulation, 'returned');
        }

        return back()->with('success', 'Pengembalian berhasil dikonfirmasi! Stok barang sekarang: ' . $item->fresh()->stock);
    }
}