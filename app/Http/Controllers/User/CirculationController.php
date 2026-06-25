<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Unit;
use App\Models\Circulation;
use Illuminate\Http\Request;

class CirculationController extends Controller
{
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
        
        $circulations = $query->orderByRaw("FIELD(status, 'pending', 'approved', 'returned', 'rejected')")
            ->latest()
            ->paginate(10);
        
        $stats = [
            'all' => Circulation::where('user_id', $userId)->count(),
            'pending' => Circulation::where('user_id', $userId)->where('status', 'pending')->count(),
            'approved' => Circulation::where('user_id', $userId)->where('status', 'approved')->count(),
            'returned' => Circulation::where('user_id', $userId)->where('status', 'returned')->count(),
            'rejected' => Circulation::where('user_id', $userId)->where('status', 'rejected')->count(),
        ];
        
        return view('user.circulations.index', compact('circulations', 'stats'));
    }

    public function create(Request $request)
    {
        $units = Unit::where('is_active', true)->get();
        
        $items = Item::where('status', 'available')
            ->with('unit')
            ->get();
        
        $selectedItem = null;
        if ($request->has('item')) {
            $selectedItem = Item::find($request->item);
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
        
        if ($item->status !== 'available') {
            return back()->with('error', 'Barang sedang tidak tersedia.');
        }
        
        $activeCirculation = Circulation::where('item_id', $item->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();
            
        if ($activeCirculation) {
            return back()->with('error', 'Barang sedang dalam proses peminjaman.');
        }
        
        Circulation::create([
            'item_id' => $request->item_id,
            'user_id' => auth()->id(),
            'borrower_name' => $request->borrower_name,
            'borrow_date' => now(),
            'expected_return_date' => $request->expected_return_date,
            'purpose' => $request->purpose,
            'status' => 'pending',
        ]);

        return redirect()->route('user.circulations.index')
            ->with('success', 'Peminjaman berhasil diajukan! Menunggu persetujuan admin unit.');
    }

    // ==================== METHOD RETURN BARANG ====================
   public function returnItem(Circulation $circulation)
{
    if ($circulation->user_id !== auth()->id()) {
        abort(403);
    }
    
    if ($circulation->status !== 'approved') {
        return back()->with('error', 'Hanya peminjaman yang disetujui yang bisa dikembalikan.');
    }
    
    // Update status sirkulasi
    $circulation->status = 'returned';
    $circulation->return_date = now();
    $circulation->save();
    
    // 🔥 UPDATE STATUS BARANG MENJADI AVAILABLE KEMBALI
    $item = $circulation->item;
    $item->status = 'available';
    $item->save();
    
    return redirect()->route('user.circulations.index')
        ->with('success', 'Barang berhasil dikembalikan! Terima kasih.');
}
}