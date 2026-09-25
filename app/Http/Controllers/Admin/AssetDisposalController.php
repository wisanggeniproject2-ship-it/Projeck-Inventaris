<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetDisposal;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AssetDisposalController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * 📋 Daftar semua pengajuan penghapusan aset
     */
    public function index(Request $request)
    {
        $query = AssetDisposal::with(['item', 'itemStock', 'user', 'approver']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $disposals = $query->latest()->paginate(10);

        return view('admin.disposals.index', compact('disposals'));
    }

    /**
     * 👁️ Detail pengajuan
     */
    public function show(AssetDisposal $disposal)
    {
        $disposal->load(['item', 'itemStock', 'user', 'approver']);
        return view('admin.disposals.show', compact('disposal'));
    }

    /**
     * ✅ APPROVE — setujui penghapusan
     * 
     * Yang terjadi otomatis:
     * 1. item_stocks.status → 'disposed' (kode stok di-nonaktifkan)
     * 2. items.stock → berkurang 1
     * 3. items.disposed_stock → bertambah 1
     * 4. asset_disposals.status → 'approved'
     */
    public function approve(AssetDisposal $disposal)
    {
        // Cek status
        if (!$disposal->isPending()) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $item = $disposal->item;
        if (!$item) {
            return back()->with('error', 'Barang tidak ditemukan.');
        }

        // 🔥 Cek kode stok spesifik
        $itemStock = $disposal->itemStock;

        if (!$itemStock) {
            return back()->with('error', 'Kode stok tidak ditemukan. Hubungi admin untuk migrasi data.');
        }

        if ($itemStock->status !== 'available') {
            return back()->with('error',
                'Kode stok "' . $itemStock->stock_code . '" sudah tidak tersedia (status: ' . $itemStock->status . ').');
        }

        // Cek stok masih mencukupi
        if ($item->stock < 1) {
            return back()->with('error',
                'Stok barang sudah habis (sisa: ' . $item->stock . ').');
        }

        // 🔥 PROSES APPROVE
        // Method approve() di Model sudah handle:
        // - update item_stock → disposed
        // - kurangi items.stock
        // - tambah items.disposed_stock
        // - update status disposal
        $disposal->approve(auth()->id());

        // 🔔 Notif ke user pengaju + monitoring ke Super Admin
        $this->notificationService->sendDisposalNotification($disposal, 'approved');

        return back()->with('success',
            'Kode stok "' . ($disposal->stock_code ?? '-') . '" berhasil dihapus. Sisa stok ' .
            $item->name . ': ' . $item->fresh()->stock . ' unit.');
    }

    /**
     * ❌ REJECT — tolak pengajuan dengan alasan
     */
    public function reject(Request $request, AssetDisposal $disposal)
    {
        if (!$disposal->isPending()) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:5|max:500',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            'rejection_reason.min'      => 'Alasan minimal 5 karakter.',
            'rejection_reason.max'      => 'Alasan maksimal 500 karakter.',
        ]);

        $disposal->reject($request->rejection_reason);

        // 🔔 Notif ke user pengaju
        $this->notificationService->sendDisposalNotification($disposal, 'rejected');

        return back()->with('success', 'Pengajuan penghapusan ditolak.');
    }
}