<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AssetDisposal;
use App\Models\Item;
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
     * 📋 Riwayat pengajuan penghapusan milik user yang sedang login
     */
    public function index()
    {
        $disposals = AssetDisposal::with(['item', 'approver'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('user.disposals.index', compact('disposals'));
    }

    /**
     * 📝 Form pengajuan penghapusan aset
     */
    public function create(Request $request)
    {
        // Barang bisa di-prefill lewat query string ?item_id=xx (dari halaman detail barang)
        $selectedItem = null;
        if ($request->filled('item_id')) {
            $selectedItem = Item::find($request->item_id);
        }

        $items = Item::where('status', '!=', 'disposed')->orderBy('name')->get();

        return view('user.disposals.create', compact('items', 'selectedItem'));
    }

    /**
     * 💾 Simpan pengajuan penghapusan
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'reason' => 'required|string|min:10|max:500',
        ], [
            'item_id.required' => 'Pilih barang yang mau diajukan.',
            'reason.required' => 'Alasan wajib diisi.',
            'reason.min' => 'Alasan minimal 10 karakter, jelasin kondisi barangnya ya.',
        ]);

        $item = Item::findOrFail($request->item_id);

        if ($item->isDisposed()) {
            return back()->with('error', 'Barang ini sudah dihapus dari sistem.');
        }

        if ($item->hasPendingDisposalRequest()) {
            return back()->with('error', 'Barang ini sudah ada pengajuan penghapusan yang masih menunggu konfirmasi.');
        }

        $disposal = AssetDisposal::create([
            'item_id' => $item->id,
            'user_id' => auth()->id(),
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        // 🔔 Notif ke Super Admin
        $this->notificationService->sendDisposalNotification($disposal, 'pending');

        return redirect()->route('user.disposals.index')
            ->with('success', 'Pengajuan penghapusan aset berhasil dikirim! Menunggu konfirmasi Super Admin.');
    }
}