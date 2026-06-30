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

    public function create(Request $request)
    {
        $units = Unit::where('is_active', true)->get();
        
        // HANYA TAMPILKAN BARANG YANG BISA DIPINJAM (AVAILABLE + KONDISI BAIK)
        $items = Item::where('status', 'available')
            ->where('condition', 'baik')
            ->with('unit')
            ->get();
        
        $selectedItem = null;
        if ($request->has('item')) {
            $selectedItem = Item::find($request->item);
            // Cek apakah barang bisa dipinjam
            if ($selectedItem && !$selectedItem->canBeBorrowed()) {
                return redirect()->route('user.items.index')
                    ->with('error', 'Barang ini tidak tersedia untuk dipinjam.');
            }
        }
        
        return view('user.circulations.create', compact('units', 'items', 'selectedItem'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'borrower_name' => 'required|string|max:100',
            'expected_return_date' => 'required|date|after:today',
            'purpose' => 'required|string',
        ]);
        
        $item = Item::findOrFail($request->item_id);
        
        // CEK APAKAH BARANG BISA DIPINJAM (STATUS AVAILABLE + KONDISI BAIK)
        if (!$item->canBeBorrowed()) {
            return back()->with('error', 'Barang tidak tersedia untuk dipinjam.');
        }
        
        // CEK APAKAH BARANG SEDANG DALAM PROSES PEMINJAMAN
        $activeCirculation = Circulation::where('item_id', $item->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();
            
        if ($activeCirculation) {
            return back()->with('error', 'Barang sedang dalam proses peminjaman.');
        }
        
        $circulation = Circulation::create([
            'item_id' => $request->item_id,
            'user_id' => auth()->id(),
            'borrower_name' => $request->borrower_name,
            'borrow_date' => now(),
            'expected_return_date' => $request->expected_return_date,
            'purpose' => $request->purpose,
            'status' => 'pending',
        ]);

        // 🔥 KIRIM NOTIFIKASI KE ADMIN UNIT (pending)
        $this->notificationService->sendCirculationNotification($circulation, 'pending');

        return redirect()->route('user.circulations.index')
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

        // 🔥 KIRIM NOTIFIKASI KE ADMIN UNIT (return_pending)
        $this->notificationService->sendCirculationNotification($circulation, 'return_pending');
        
        return redirect()->route('user.circulations.index')
            ->with('success', 'Permintaan pengembalian berhasil diajukan! Menunggu konfirmasi admin unit.');
    }
}