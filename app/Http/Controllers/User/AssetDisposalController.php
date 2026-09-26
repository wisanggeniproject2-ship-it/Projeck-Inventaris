<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AssetDisposal;
use App\Models\Item;
use App\Models\ItemStock;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $disposals = AssetDisposal::with(['item', 'itemStock', 'approver'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('user.disposals.index', compact('disposals'));
    }

    /**
     * 📝 Form pengajuan penghapusan aset
     * 
     * User bisa pilih barang + kode stok spesifik yang rusak
     */
    public function create(Request $request)
    {
        // Barang bisa di-prefill lewat ?item_id=xx (dari halaman detail barang)
        $selectedItem = null;
        $stockCodes   = [];

        if ($request->filled('item_id')) {
            $selectedItem = Item::find($request->item_id);

            if ($selectedItem) {
                // 🔥 Ambil kode stok yang masih available
                $stockCodes = $selectedItem->availableStockCodes()->get();
            }
        }

        // Hanya barang yang stoknya masih ada yang bisa diajukan
        $items = Item::where('status', '!=', 'disposed')
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        return view('user.disposals.create', compact('items', 'selectedItem', 'stockCodes'));
    }

    /**
     * 💾 Simpan pengajuan penghapusan
     * 
     * User pilih: item + kode stok spesifik (item_stock_id) + alasan + foto
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id'       => 'required|exists:items,id',
            'item_stock_id' => 'required|exists:item_stocks,id',
            'reason'        => 'required|string|min:10|max:500',
            'photos'        => 'required|array|min:1',
            'photos.*'      => 'image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'item_id.required'       => 'Pilih barang yang mau diajukan.',
            'item_id.exists'         => 'Barang tidak valid.',
            'item_stock_id.required' => 'Pilih kode stok yang rusak.',
            'item_stock_id.exists'   => 'Kode stok tidak valid.',
            'reason.required'        => 'Alasan wajib diisi.',
            'reason.min'             => 'Alasan minimal 10 karakter, jelasin kondisi barangnya ya.',
            'reason.max'             => 'Alasan maksimal 500 karakter.',
            'photos.required'        => 'Wajib upload minimal 1 foto sebagai bukti kerusakan.',
            'photos.array'           => 'Format upload foto tidak valid.',
            'photos.min'             => 'Wajib upload minimal 1 foto.',
            'photos.*.image'         => 'File harus berupa gambar.',
            'photos.*.mimes'         => 'Format foto harus JPG/JPEG/PNG.',
            'photos.*.max'           => 'Ukuran foto maksimal 2MB per foto.',
        ]);

        // Cek barang
        $item = Item::findOrFail($request->item_id);

        if ($item->isDisposed()) {
            return back()->withInput()
                ->with('error', 'Barang ini sudah dihapus dari sistem.');
        }

        // 🔥 Cek kode stok
        $itemStock = ItemStock::where('id', $request->item_stock_id)
            ->where('item_id', $item->id)
            ->first();

        if (!$itemStock) {
            return back()->withInput()
                ->with('error', 'Kode stok tidak ditemukan untuk barang ini.');
        }

        if ($itemStock->status !== 'available') {
            return back()->withInput()
                ->with('error', 'Kode stok ini sudah tidak tersedia (status: ' . $itemStock->status . ').');
        }

        // 🔥 Cek sudah ada pengajuan pending untuk kode stok ini?
        $existingDisposal = AssetDisposal::where('item_stock_id', $itemStock->id)
            ->where('status', 'pending')
            ->exists();

        if ($existingDisposal) {
            return back()->withInput()
                ->with('error', 'Kode stok ini sudah diajukan penghapusan dan masih menunggu konfirmasi.');
        }

        // Simpan foto bukti
        $photoPaths = [];
        foreach ($request->file('photos', []) as $photo) {
            $photoPaths[] = $photo->store('disposals', 'public');
        }

        // 🔥 Buat pengajuan
        $disposal = AssetDisposal::create([
            'item_id'       => $item->id,
            'item_stock_id' => $itemStock->id,          // FK ke item_stocks
            'stock_code'    => $itemStock->stock_code,  // Simpan kode (history)
            'user_id'       => auth()->id(),
            'quantity'      => 1,                        // Fixed 1 (per unit)
            'reason'        => $request->reason,
            'photos'        => $photoPaths,
            'status'        => 'pending',
        ]);

        // 🔔 Notif ke Super Admin
        $this->notificationService->sendDisposalNotification($disposal, 'pending');

        return redirect()->route('user.disposals.index')
            ->with('success', 'Pengajuan penghapusan berhasil dikirim! Menunggu konfirmasi Super Admin.');
    }

    /**
     * 🔥🔥🔥 AJAX: Ambil daftar kode stok yang available
     * 
     * Route: GET /user/items/{item}/stock-codes
     * Name:  user.items.stock-codes
     * 
     * Dipakai oleh form create disposal untuk menampilkan dropdown kode stok
     * saat user memilih barang.
     */
    public function getStockCodes(Item $item)
    {
        $stockCodes = $item->availableStockCodes()
            ->get()
            ->map(fn($s) => [
                'id'   => $s->id,
                'no'   => $s->stock_number,
                'code' => $s->stock_code,
            ]);

        return response()->json([
            'success'    => true,
            'item_id'    => $item->id,
            'item_name'  => $item->name,
            'stockCodes' => $stockCodes,
        ]);
    }

    /**
     * ============================================================
     * 🔥🔥🔥 CETAK BERITA ACARA PENGHAPUSAN
     * ============================================================
     * 
     * Hanya bisa dicetak kalau:
     * 1. Disposal milik user yang sedang login (ownership check)
     * 2. Status disposal = approved
     * 
     * Route: GET /user/disposals/{disposal}/berita-acara
     * Name:  user.disposals.berita-acara
     * 
     * 🔥 FIX:
     * - margin 0 karena background template full A4
     * - Posisi konten diatur oleh padding di blade
     * - dpi 150 & parser HTML5 biar stabil
     */
    public function beritaAcara(AssetDisposal $disposal)
    {
        // 🔒 Cek ownership — pengajuan ini punya user yang login?
        if ($disposal->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pengajuan ini.');
        }

        // 🔒 Cek status — hanya bisa cetak kalau sudah approved
        if ($disposal->status !== 'approved') {
            return back()->with('error', 
                'Berita acara hanya bisa dicetak untuk pengajuan yang sudah disetujui.'
            );
        }

        // 🔥 Load relasi yang dibutuhkan untuk template PDF
        $disposal->load([
            'item.category',
            'item.unit',
            'item.fundingSource',
            'itemStock',
            'user',
            'approver',
        ]);

        // 🔥 Render PDF
        // Margin 0 karena background template full A4
        // Posisi konten diatur oleh padding di blade (.konten)
        $pdf = Pdf::loadView('user.disposals.berita-acara-pdf', [
            'disposal' => $disposal,
        ])
            ->setPaper('a4', 'portrait')
            ->setOption([
                // 🔥 Margin 0 — background template full A4
                'margin_top'    => 0,
                'margin_right'  => 0,
                'margin_bottom' => 0,
                'margin_left'   => 0,

                // 🔥 Parser & rendering options biar stabil
                'dpi'                  => 150,
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'Times New Roman',
            ]);

        // 🔥 Nama file
        $cleanCode = str_replace(
            ['/', '.', ' '],
            '-',
            $disposal->stock_code ?? $disposal->item->code ?? 'item'
        );
        $fileName = 'berita-acara-' . $cleanCode . '.pdf';

        return $pdf->download($fileName);
    }
}