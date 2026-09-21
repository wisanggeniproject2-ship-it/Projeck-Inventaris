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
        $query = AssetDisposal::with(['item', 'user', 'approver']);

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
        $disposal->load(['item', 'user', 'approver']);
        return view('admin.disposals.show', compact('disposal'));
    }

    /**
     * ✅ APPROVE — setujui penghapusan, barang otomatis dihapus dari daftar aset
     */
    public function approve(AssetDisposal $disposal)
    {
        if (!$disposal->isPending()) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        if ($disposal->quantity > $disposal->item->stock) {
            return back()->with('error',
                'Stok barang sudah berubah dan tidak mencukupi untuk menyetujui pengajuan ini (sisa stok: ' . $disposal->item->stock . ').');
        }

        $disposal->approve(auth()->id());

        // 🔔 Notif ke user pengaju + monitoring ke Super Admin
        $this->notificationService->sendDisposalNotification($disposal, 'approved');

        return back()->with('success',
            $disposal->quantity . ' unit "' . $disposal->item->name . '" berhasil dihapus dari stok.');
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
            'rejection_reason.min' => 'Alasan minimal 5 karakter.',
        ]);

        $disposal->reject($request->rejection_reason);

        // 🔔 Notif ke user pengaju
        $this->notificationService->sendDisposalNotification($disposal, 'rejected');

        return back()->with('success', 'Pengajuan penghapusan ditolak.');
    }
}