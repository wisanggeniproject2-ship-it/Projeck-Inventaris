<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\Unit;
use App\Models\Circulation;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            ->with(['item', 'item.unit', 'itemStock']);
        
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
            'all'            => Circulation::where('user_id', $userId)->count(),
            'pending'        => Circulation::where('user_id', $userId)->where('status', 'pending')->count(),
            'approved'       => Circulation::where('user_id', $userId)->where('status', 'approved')->count(),
            'return_pending' => Circulation::where('user_id', $userId)->where('status', 'return_pending')->count(),
            'returned'       => Circulation::where('user_id', $userId)->where('status', 'returned')->count(),
            'rejected'       => Circulation::where('user_id', $userId)->where('status', 'rejected')->count(),
        ];
        
        return view('user.circulations.index', compact('circulations', 'stats'));
    }

    /**
     * 🔥 FORM CREATE — barang & unit auto-isi dari ?item=ID
     * 
     * User bisa pilih kode stok spesifik yang mau dipinjam
     */
    public function create(Request $request)
    {
        // WAJIB ada parameter ?item=ID
        if (!$request->filled('item')) {
            return redirect()
                ->route('user.items.index')
                ->with('error', 'Silakan pilih barang terlebih dahulu.');
        }

        // Ambil item + relasi
        $selectedItem = Item::with(['category', 'unit', 'fundingSource'])
            ->find($request->item);

        if (!$selectedItem) {
            return redirect()
                ->route('user.items.index')
                ->with('error', 'Barang tidak ditemukan.');
        }

        // 🔥 Ambil kode stok yang AVAILABLE (bukan yang sedang dipinjam)
        $availableStockCodes = $selectedItem->availableStockCodes()->get();

        if ($availableStockCodes->isEmpty()) {
            return redirect()
                ->route('user.items.index')
                ->with('error', 'Semua unit barang ini sedang dipinjam atau tidak tersedia.');
        }

        // Cek apakah user sudah punya peminjaman aktif untuk item ini
        $existingCirculation = Circulation::where('item_id', $selectedItem->id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'approved', 'return_pending'])
            ->exists();

        if ($existingCirculation) {
            return redirect()
                ->route('user.circulations.index')
                ->with('error', 'Anda sudah memiliki peminjaman aktif untuk barang ini.');
        }

        $units = Unit::where('is_active', true)->get();

        return view('user.circulations.create', compact('selectedItem', 'units', 'availableStockCodes'));
    }

    /**
     * 🔥 STORE — simpan peminjaman (multi-select kode stok)
     * 
     * User pilih:
     *  - item_stock_ids[] (1 atau lebih kode stok spesifik yang mau dipinjam)
     *  - borrow_date + borrow_hour + borrow_minute
     *  - expected_return_date + return_hour + return_minute
     *  - borrower_name
     *  - purpose
     * 
     * 1 baris Circulation dibuat per kode stok yang dipilih.
     */
    public function store(Request $request)
    {
        // ============================================================
        // 1. VALIDASI INPUT
        // ============================================================
        $request->validate([
            'item_id'              => 'required|exists:items,id',
            'item_stock_ids'       => 'required|array|min:1',
            'item_stock_ids.*'     => 'integer|exists:item_stocks,id',
            'borrower_name'        => 'required|string|max:100',
            'borrow_date'          => 'required|date|after_or_equal:today',
            'borrow_hour'          => 'required|numeric|between:0,23',
            'borrow_minute'        => 'required|numeric|between:0,59',
            'expected_return_date' => 'required|date|after_or_equal:borrow_date',
            'return_hour'          => 'required|numeric|between:0,23',
            'return_minute'        => 'required|numeric|between:0,59',
            'purpose'              => 'required|string|max:1000',
        ], [
            'item_id.required'                    => 'Barang wajib dipilih.',
            'item_id.exists'                      => 'Barang tidak valid.',
            'item_stock_ids.required'             => 'Pilih minimal 1 kode stok yang mau dipinjam.',
            'item_stock_ids.min'                  => 'Pilih minimal 1 kode stok yang mau dipinjam.',
            'item_stock_ids.*.exists'             => 'Salah satu kode stok tidak valid.',
            'borrower_name.required'              => 'Nama peminjam wajib diisi.',
            'borrower_name.max'                   => 'Nama peminjam maksimal 100 karakter.',
            'borrow_date.required'                => 'Tanggal pinjam wajib diisi.',
            'borrow_date.after_or_equal'          => 'Tanggal pinjam tidak boleh di masa lalu.',
            'borrow_hour.required'                => 'Jam pinjam wajib diisi.',
            'borrow_hour.numeric'                 => 'Jam pinjam harus berupa angka.',
            'borrow_hour.between'                 => 'Jam pinjam harus antara 0-23.',
            'borrow_minute.required'              => 'Menit pinjam wajib diisi.',
            'borrow_minute.numeric'               => 'Menit pinjam harus berupa angka.',
            'borrow_minute.between'               => 'Menit pinjam harus antara 0-59.',
            'expected_return_date.required'       => 'Tanggal kembali wajib diisi.',
            'expected_return_date.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal pinjam.',
            'return_hour.required'                => 'Jam kembali wajib diisi.',
            'return_hour.numeric'                 => 'Jam kembali harus berupa angka.',
            'return_hour.between'                 => 'Jam kembali harus antara 0-23.',
            'return_minute.required'              => 'Menit kembali wajib diisi.',
            'return_minute.numeric'               => 'Menit kembali harus berupa angka.',
            'return_minute.between'               => 'Menit kembali harus antara 0-59.',
            'purpose.required'                    => 'Tujuan peminjaman wajib diisi.',
            'purpose.max'                         => 'Tujuan peminjaman maksimal 1000 karakter.',
        ]);

        // ============================================================
        // 2. AMBIL ITEM & VALIDASI SEMUA KODE STOK YANG DIPILIH
        // ============================================================
        $item = Item::findOrFail($request->item_id);

        $itemStocks = ItemStock::whereIn('id', $request->item_stock_ids)
            ->where('item_id', $item->id)
            ->get();

        if ($itemStocks->count() !== count($request->item_stock_ids)) {
            return back()->withInput()
                ->with('error', 'Sebagian kode stok yang dipilih tidak valid untuk barang ini.');
        }

        $notAvailable = $itemStocks->where('status', '!=', 'available');
        if ($notAvailable->isNotEmpty()) {
            $codes = $notAvailable->pluck('stock_code')->implode(', ');
            return back()->withInput()
                ->with('error', 'Kode stok berikut sudah tidak tersedia: ' . $codes);
        }

        $activeStockIds = Circulation::whereIn('item_stock_id', $itemStocks->pluck('id'))
            ->whereIn('status', ['pending', 'approved', 'return_pending'])
            ->pluck('item_stock_id')
            ->toArray();

        if (!empty($activeStockIds)) {
            $codes = $itemStocks->whereIn('id', $activeStockIds)->pluck('stock_code')->implode(', ');
            return back()->withInput()
                ->with('error', 'Kode stok berikut sedang dalam proses peminjaman: ' . $codes);
        }

        $userExisting = Circulation::where('item_id', $item->id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'approved', 'return_pending'])
            ->exists();

        if ($userExisting) {
            return back()
                ->withInput()
                ->with('error', 'Anda sudah memiliki peminjaman aktif untuk barang ini.');
        }

        // ============================================================
        // 3. GABUNG TANGGAL + JAM PINJAM → DATETIME
        // ============================================================
        try {
            $borrowTime = str_pad($request->borrow_hour, 2, '0', STR_PAD_LEFT)
                        . ':' . str_pad($request->borrow_minute, 2, '0', STR_PAD_LEFT);

            $borrowDateTime = Carbon::createFromFormat(
                'Y-m-d H:i',
                $request->borrow_date . ' ' . $borrowTime
            );
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Format tanggal atau jam pinjam tidak valid.');
        }

        if ($borrowDateTime->isPast()) {
            return back()
                ->withInput()
                ->with('error', 'Waktu pinjam tidak boleh di masa lalu.');
        }

        // ============================================================
        // 4. GABUNG TANGGAL + JAM TENGGAT → DATETIME
        // ============================================================
        try {
            $returnTime = str_pad($request->return_hour, 2, '0', STR_PAD_LEFT)
                        . ':' . str_pad($request->return_minute, 2, '0', STR_PAD_LEFT);

            $expectedReturn = Carbon::createFromFormat(
                'Y-m-d H:i',
                $request->expected_return_date . ' ' . $returnTime
            );
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Format tanggal atau jam kembali tidak valid.');
        }

        if ($expectedReturn->lte($borrowDateTime)) {
            return back()
                ->withInput()
                ->with('error', 'Tenggat pengembalian harus setelah waktu pinjam.');
        }

        // ============================================================
        // 5. VALIDASI DURASI
        // ============================================================
        if ($expectedReturn->lessThan($borrowDateTime->copy()->addMinutes(30))) {
            return back()
                ->withInput()
                ->with('error', 'Durasi peminjaman minimal 30 menit.');
        }

        if ($expectedReturn->greaterThan($borrowDateTime->copy()->addDays(7))) {
            return back()
                ->withInput()
                ->with('error', 'Durasi peminjaman maksimal 7 hari.');
        }

        // ============================================================
        // 6. BUAT CIRCULATION — 1 BARIS PER KODE STOK YANG DIPILIH
        // ============================================================
        $createdCirculations = [];

        DB::transaction(function () use (&$createdCirculations, $itemStocks, $item, $request, $borrowDateTime, $expectedReturn) {
            foreach ($itemStocks as $itemStock) {
                $createdCirculations[] = Circulation::create([
                    'item_id'              => $item->id,
                    'item_stock_id'        => $itemStock->id,
                    'stock_code'           => $itemStock->stock_code,
                    'user_id'              => auth()->id(),
                    'borrower_name'        => $request->borrower_name,
                    'borrow_date'          => $borrowDateTime,
                    'expected_return_date' => $expectedReturn,
                    'purpose'              => $request->purpose,
                    'status'               => 'pending',
                ]);
            }
        });

        // ============================================================
        // 7. KIRIM NOTIFIKASI KE ADMIN UNIT (1 notif per unit)
        // ============================================================
        foreach ($createdCirculations as $circulation) {
            $this->notificationService->sendCirculationNotification($circulation, 'pending');
        }

        $codesList = $itemStocks->pluck('stock_code')->implode(', ');
        $count = count($createdCirculations);

        return redirect()
            ->route('user.circulations.index')
            ->with('success', $count . ' unit berhasil diajukan! Menunggu persetujuan admin unit. Kode: ' . $codesList);
    }

    /**
     * 🔥 REQUEST RETURN — user ajukan pengembalian
     */
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

        // Kirim notifikasi ke admin unit
        $this->notificationService->sendCirculationNotification($circulation, 'return_pending');
        
        return redirect()
            ->route('user.circulations.index')
            ->with('success', 'Permintaan pengembalian berhasil diajukan! Menunggu konfirmasi admin unit.');
    }
}