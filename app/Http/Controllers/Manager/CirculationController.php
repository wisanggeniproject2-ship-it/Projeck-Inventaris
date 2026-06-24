<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Circulation;
use Illuminate\Http\Request;

class CirculationController extends Controller
{
    public function __construct()
    {
        // HAPUS: $this->middleware('role:user');
    }

    public function index()
    {
        $circulations = Circulation::where('user_id', auth()->id())
            ->with('item')
            ->latest()
            ->paginate(10);
        
        return view('user.circulations.index', compact('circulations'));
    }

    public function create(Request $request)
    {
        $items = Item::where('unit_id', auth()->user()->unit_id)
            ->where('status', 'available')
            ->get();
        
        $selectedItem = null;
        if ($request->has('item')) {
            $selectedItem = Item::find($request->item);
        }
        
        return view('user.circulations.create', compact('items', 'selectedItem'));
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
            ->with('success', 'Peminjaman berhasil diajukan.');
    }
}