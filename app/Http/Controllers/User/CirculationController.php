<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Unit;
use App\Models\Circulation;
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
        $userId = auth()->id();
        
        $query = Circulation::where('user_id', $userId)
            ->with(['item', 'item.unit']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('item', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        
        $circulations = $query->orderByRaw("FIELD(status, 'pending', 'approved', 'return_pending', 'returned', 'rejected')")
            ->latest()
            ->paginate(10);
        
        $stats = [
            'all' => Circulation::where('user_id', $userId)->count(),
            'pending' => Circulation::where('user_id', $userId)->where('status', 'pending')->count(),
            'approved' => Circulation::where('user_id', $userId)->where('status', 'approved')->count(),
            'return_pending' => Circulation::where('user_id', $userId)->where('status', 'return_pending')->count(),
            'returned' => Circulation::where('user_id', $userId)->where('status', 'returned')->count(),
            'rejected' => Circulation::where('user_id', $userId)->where('status', 'rejected')->count(),
        ];
        
        return view('user.circulations.index', compact('circulations', 'stats'));
    }

    /**
     * 🔥 FORM CREATE — barang & unit auto-isi dari ?item=ID
     *
     * Alur baru:
     * - User pilih barang di halaman items
     * - Klik "Pinjam" → modal pilih unit
     * - Klik unit → redirect ke sini dengan ?item=ID
     * - Form auto-isi barang & unit (readonly)
     */
    public function create(Request $request)
    {
        // 🔥 WAJIB ada parameter ?item=ID
        // Kalau tidak ada, redirect ke daftar barang
        if (!$request->filled('item')) {
            return redirect()
                ->route('user.items.index')
                ->with('error', 'Silakan pilih barang terlebih dahulu.');
        }

        // 🔥 Ambil item + relasi
        $selectedItem = Item::with(['category', 'unit', 'fundingSource'])
            ->find($request->item);

        // Kalau item tidak ditemukan
        if (!$selectedItem) {
            return redirect()
                ->route('user.items.index')
                ->with('error', 'Barang tidak ditemukan.');
        }

        // 🔥 Cek apakah barang bisa dipinjam
        if (!$selectedItem->canBeBorrowed()) {
            return redirect()
                ->route('user.items.index')
                ->with('error', 'Barang ini tidak bisa dipinjam saat ini (stok habis / rusak / sedang dipinjam).');
        }

        // 🔥 Cek apakah sudah ada peminjaman aktif untuk item ini oleh user
        $existingCirculation = Circulation::where('item_id', $selectedItem->id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'approved', 'return_pending'])
            ->exists();

        if ($existingCirculation) {
            return redirect()
                ->route('user.circulations.index')
                ->with('error', 'Anda sudah memiliki peminjaman aktif untuk barang ini.');
        }

        // Data unit (untuk dropdown — kalau suatu saat butuh)
        $units = Unit::where('is_active', true)->get();

        return view('user.circulations.create', compact('selectedItem', 'units'));
    }

    /**
     * 🔥 STORE — simpan peminjaman
     */
    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'item_id'              => 'required|exists:items,id',
            'borrower_name'        => 'required|string|max:100',
            'expected_return_date' => 'required|date|after:today',
            'purpose'              => 'required|string|max:1000',
        ], [
            'item_id.required'              => 'Barang wajib dipilih.',
            'item_id.exists'                => 'Barang tidak valid.',
            'borrower_name.required'        => 'Nama peminjam wajib diisi.',
            'borrower_name.max'             => 'Nama peminjam maksimal 100 karakter.',
            'expected_return_date.required' => 'Tanggal kembali wajib diisi.',
            'expected_return_date.after'    => 'Tanggal kembali harus setelah hari ini.',
            'purpose.required'              => 'Tujuan peminjaman wajib diisi.',
            'purpose.max'                   => 'Tujuan peminjaman maksimal 1000 karakter.',
        ]);

        // Ambil item
        $item = Item::findOrFail($request->item_id);

        // 🔥 Cek apakah barang masih bisa dipinjam
        if (!$item->canBeBorrowed()) {
            return back()
                ->withInput()
                ->with('error', 'Barang tidak tersedia untuk dipinjam (stok habis / rusak / sedang dipinjam).');
        }

        // 🔥 Cek apakah sudah ada peminjaman aktif untuk item ini
        $activeCirculation = Circulation::where('item_id', $item->id)
            ->whereIn('status', ['pending', 'approved', 'return_pending'])
            ->exists();

        if ($activeCirculation) {
            return back()
                ->withInput()
                ->with('error', 'Barang sedang dalam proses peminjaman oleh pihak lain.');
        }

        // 🔥 Cek apakah user sudah punya peminjaman aktif untuk item ini
        $userExisting = Circulation::where('item_id', $item->id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'approved', 'return_pending'])
            ->exists();

        if ($userExisting) {
            return back()
                ->withInput()
                ->with('error', 'Anda sudah memiliki peminjaman aktif untuk barang ini.');
        }

        // 🔥 Buat circulation
        $circulation = Circulation::create([
            'item_id'              => $item->id,
            'user_id'              => auth()->id(),
            'borrower_name'        => $request->borrower_name,
            'borrow_date'          => now(),
            'expected_return_date' => $request->expected_return_date,
            'purpose'              => $request->purpose,
            'status'               => 'pending',
        ]);

        // 🔥 Kirim notifikasi ke admin unit (pending)
        $this->notificationService->sendCirculationNotification($circulation, 'pending');

        return redirect()
            ->route('user.circulations.index')
            ->with('success', 'Peminjaman berhasil diajukan! Menunggu persetujuan admin unit.');
    }

    // ==================== REQUEST RETURN (USER AJUKAN PENGEMBALIAN) ====================
    public function requestReturn(Circulation $circulation)
    {
        // Pastikan ini milik user yang login
        if ($circulation->user_id !== auth()->id()) {
            abort(403);
        }
        
        // Hanya bisa request return jika status approved
        if ($circulation->status !== 'approved') {
            return back()->with('error', 'Hanya peminjaman yang disetujui yang bisa dikembalikan.');
        }
        
        // Update status jadi return_pending (menunggu konfirmasi admin)
        $circulation->status = 'return_pending';
        $circulation->save();

        // 🔥 Kirim notifikasi ke admin unit (return_pending)
        $this->notificationService->sendCirculationNotification($circulation, 'return_pending');
        
        return redirect()
            ->route('user.circulations.index')
            ->with('success', 'Permintaan pengembalian berhasil diajukan! Menunggu konfirmasi admin unit.');
    }
}