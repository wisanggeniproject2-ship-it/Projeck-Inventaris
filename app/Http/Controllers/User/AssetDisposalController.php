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

        // Hanya barang yang stoknya masih ada yang bisa diajukan
        $items = Item::where('status', '!=', 'disposed')
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        return view('user.disposals.create', compact('items', 'selectedItem'));
    }

    /**
     * 💾 Simpan pengajuan penghapusan
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|min:10|max:500',
            'photos' => 'required|array|min:1',
            'photos.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'item_id.required' => 'Pilih barang yang mau diajukan.',
            'quantity.required' => 'Jumlah unit rusak wajib diisi.',
            'quantity.min' => 'Jumlah unit rusak minimal 1.',
            'reason.required' => 'Alasan wajib diisi.',
            'reason.min' => 'Alasan minimal 10 karakter, jelasin kondisi barangnya ya.',
            'photos.required' => 'Wajib upload minimal 1 foto sebagai bukti kerusakan.',
            'photos.*.image' => 'File harus berupa gambar.',
            'photos.*.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $item = Item::findOrFail($request->item_id);

        if ($item->isDisposed()) {
            return back()->withInput()->with('error', 'Barang ini sudah dihapus dari sistem.');
        }

        if ($item->hasPendingDisposalRequest()) {
            return back()->withInput()->with('error', 'Barang ini sudah ada pengajuan penghapusan yang masih menunggu konfirmasi.');
        }

        if ($request->quantity > $item->stock) {
            return back()->withInput()->with('error',
                'Jumlah unit rusak (' . $request->quantity . ') melebihi stok tersedia (' . $item->stock . ').');
        }

        // Simpan foto bukti — pola sama dengan upload image di Item
        $photoPaths = [];
        foreach ($request->file('photos', []) as $photo) {
            $photoPaths[] = $photo->store('disposals', 'public');
        }

        $disposal = AssetDisposal::create([
            'item_id' => $item->id,
            'user_id' => auth()->id(),
            'quantity' => $request->quantity,
            'reason' => $request->reason,
            'photos' => $photoPaths,
            'status' => 'pending',
        ]);

        // 🔔 Notif ke Super Admin
        $this->notificationService->sendDisposalNotification($disposal, 'pending');

        return redirect()->route('user.disposals.index')
            ->with('success', 'Pengajuan penghapusan aset berhasil dikirim! Menunggu konfirmasi Super Admin.');
    }
}