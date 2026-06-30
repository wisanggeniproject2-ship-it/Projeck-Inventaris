<?php

namespace App\Http\Controllers\AdminUnit;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Category;
use App\Models\Unit;
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    protected $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
        // Middleware sudah dihandle di routes
    }

    public function index(Request $request)
    {
        $unitId = auth()->user()->unit_id;
        
        $query = Item::with(['category', 'unit'])->where('unit_id', $unitId);
        
        // Filter berdasarkan kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        $items = $query->latest()->paginate(10);
        $categories = Category::all();
        
        return view('admin_unit.items.index', compact('items', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $units = Unit::where('id', auth()->user()->unit_id)->get();
        return view('admin_unit.items.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'purchase_date' => 'nullable|date',
            'condition' => 'required|in:baik,rusak,perbaikan',
            'price' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:200',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
        ]);

        $code = Item::generateCode(auth()->user()->unit_id);

        $data = [
            'code' => $code,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'unit_id' => auth()->user()->unit_id,
            'purchase_date' => $request->purchase_date,
            'condition' => $request->condition,
            'price' => $request->price,
            'location' => $request->location,
            'description' => $request->description,
            'status' => 'available',
        ];

        // Upload gambar
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('items', 'public');
        }

        $item = Item::create($data);

        // Generate QR Code
        try {
            $qrCodePath = $this->qrCodeService->generateQrCode($item);
            $item->update(['qr_code_path' => $qrCodePath]);
        } catch (\Exception $e) {
            \Log::error('QR Code generation failed: ' . $e->getMessage());
        }

        return redirect()->route('admin_unit.items.index')
            ->with('success', 'Barang berhasil ditambahkan! Kode: ' . $code);
    }

    public function show(Item $item)
    {
        if ($item->unit_id !== auth()->user()->unit_id) {
            abort(403);
        }
        
        $item->load(['category', 'unit', 'circulations.user']);
        return view('admin_unit.items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        abort(403, 'Admin unit tidak memiliki akses untuk mengedit barang.');
    }

    public function update(Request $request, Item $item)
    {
        abort(403, 'Admin unit tidak memiliki akses untuk mengupdate barang.');
    }

    public function destroy(Item $item)
    {
        abort(403, 'Admin unit tidak memiliki akses untuk menghapus barang.');
    }
}