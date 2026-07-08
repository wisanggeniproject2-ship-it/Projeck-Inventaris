<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Category;
use App\Models\Unit;
use App\Models\FundingSource;
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ItemController extends Controller
{
    protected $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    // ==================== INDEX ====================
    public function index(Request $request)
    {
        $query = Item::with(['category', 'unit', 'fundingSource']);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('unit')) {
            $query->where('unit_id', $request->unit);
        }
        
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }
        
        $items = $query->latest()->paginate(15);
        $units = Unit::where('is_active', true)->get();
        $categories = Category::all();
        
        return view('admin.items.index', compact('items', 'units', 'categories'));
    }

    // ==================== CREATE ====================
    public function create()
    {
        $categories = Category::all();
        $units = Unit::where('is_active', true)->get();
        $fundingSources = FundingSource::where('is_active', true)->get();
        return view('admin.items.create', compact('categories', 'units', 'fundingSources'));
    }

    // ==================== STORE ====================
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'purchase_date' => 'nullable|date',
            'condition' => 'required|in:baik,rusak,perbaikan',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:1',
            'location' => 'nullable|string|max:200',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
            'funding_source_id' => 'nullable|exists:funding_sources,id',
        ]);

        $code = Item::generateCode(
            $request->unit_id,
            $request->category_id,
            $request->purchase_date ? \Carbon\Carbon::parse($request->purchase_date) : null
        );

        $data = [
            'code' => $code,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'unit_id' => $request->unit_id,
            'purchase_date' => $request->purchase_date,
            'condition' => $request->condition,
            'price' => $request->price,
            'stock' => $request->stock,
            'location' => $request->location,
            'description' => $request->description,
            'funding_source_id' => $request->funding_source_id,
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

        return redirect()->route('super_admin.items.index')
            ->with('success', 'Barang berhasil ditambahkan! Kode: ' . $code . ' | Stok: ' . $request->stock);
    }

    // ==================== SHOW ====================
    public function show(Item $item)
    {
        $item->load(['category', 'unit', 'fundingSource', 'circulations.user', 'circulations.approver']);
        return view('admin.items.show', compact('item'));
    }

    // ==================== EDIT ====================
    public function edit(Item $item)
    {
        $categories = Category::all();
        $units = Unit::where('is_active', true)->get();
        $fundingSources = FundingSource::where('is_active', true)->get();
        return view('admin.items.edit', compact('item', 'categories', 'units', 'fundingSources'));
    }

    // ==================== UPDATE ====================
    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'purchase_date' => 'nullable|date',
            'condition' => 'required|in:baik,rusak,perbaikan',
            'price' => 'nullable|numeric|min:0',
            'funding_source_id' => 'nullable|exists:funding_sources,id',
            'location' => 'nullable|string|max:200',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
        ]);

        $data = $request->except(['image']);

        if ($request->hasFile('image')) {
            if ($item->image && Storage::disk('public')->exists($item->image)) {
                Storage::disk('public')->delete($item->image);
            }
            $data['image'] = $request->file('image')->store('items', 'public');
        }

        $item->update($data);

        return redirect()->route('super_admin.items.index')
            ->with('success', 'Barang berhasil diupdate!');
    }

    // ==================== DESTROY ====================
    public function destroy(Item $item)
    {
        if ($item->image && Storage::disk('public')->exists($item->image)) {
            Storage::disk('public')->delete($item->image);
        }
        
        if ($item->qr_code_path && Storage::disk('public')->exists($item->qr_code_path)) {
            Storage::disk('public')->delete($item->qr_code_path);
        }
        
        $item->delete();

        return redirect()->route('super_admin.items.index')
            ->with('success', 'Barang berhasil dihapus!');
    }

    // ==================== GENERATE PDF ====================
    public function pdf(Item $item)
    {
        $item->load(['category', 'unit', 'fundingSource']);
        
        // Bersihkan nama file - replace "/" dengan "-"
        $cleanCode = str_replace('/', '-', $item->code);
        $fileName = 'barang-' . $cleanCode . '.pdf';
        
        $pdf = Pdf::loadView('admin.items.pdf', compact('item'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download($fileName);
    }
}