<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Category;
use App\Models\Unit;
use App\Models\FundingSource;  // 🔥 TAMBAHKAN INI
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

    public function index(Request $request)
    {
        $query = Item::with(['category', 'unit', 'fundingSource']);  // 🔥 TAMBAHKAN fundingSource
        
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
        
        $items = $query->latest()->paginate(15);
        $units = Unit::where('is_active', true)->get();
        $categories = Category::all();
        
        return view('admin.items.index', compact('items', 'units', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $units = Unit::where('is_active', true)->get();
        $fundingSources = FundingSource::active()->get();  // 🔥 TAMBAHKAN INI
        return view('admin.items.create', compact('categories', 'units', 'fundingSources'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'purchase_date' => 'nullable|date',
            'condition' => 'required|in:baik,rusak,perbaikan',
            'price' => 'nullable|numeric|min:0',
            'funding_source_id' => 'nullable|exists:funding_sources,id',  // 🔥 UBAH KE funding_source_id
            'location' => 'nullable|string|max:200',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
        ]);

        $code = Item::generateCode($request->unit_id);

        $data = [
            'code' => $code,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'unit_id' => $request->unit_id,
            'purchase_date' => $request->purchase_date,
            'condition' => $request->condition,
            'price' => $request->price,
            'funding_source_id' => $request->funding_source_id,  // 🔥 UBAH KE funding_source_id
            'location' => $request->location,
            'description' => $request->description,
            'status' => 'available',
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('items', 'public');
        }

        $item = Item::create($data);

        try {
            $qrCodePath = $this->qrCodeService->generateQrCode($item);
            $item->update(['qr_code_path' => $qrCodePath]);
        } catch (\Exception $e) {
            \Log::error('QR Code generation failed: ' . $e->getMessage());
        }

        return redirect()->route('super_admin.items.index')
            ->with('success', 'Barang berhasil ditambahkan! Kode: ' . $code);
    }

    public function show(Item $item)
    {
        $item->load(['category', 'unit', 'fundingSource', 'circulations.user', 'circulations.approver']);  // 🔥 TAMBAHKAN fundingSource
        return view('admin.items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $categories = Category::all();
        $units = Unit::where('is_active', true)->get();
        $fundingSources = FundingSource::active()->get();  // 🔥 TAMBAHKAN INI
        return view('admin.items.edit', compact('item', 'categories', 'units', 'fundingSources'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'purchase_date' => 'nullable|date',
            'condition' => 'required|in:baik,rusak,perbaikan',
            'price' => 'nullable|numeric|min:0',
            'funding_source_id' => 'nullable|exists:funding_sources,id',  // 🔥 UBAH KE funding_source_id
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

    public function destroy(Item $item)
    {
        if ($item->image && Storage::disk('public')->exists($item->image)) {
            Storage::disk('public')->delete($item->image);
        }
        
        if ($item->qr_code_path) {
            Storage::disk('public')->delete($item->qr_code_path);
        }
        
        $item->delete();

        return redirect()->route('super_admin.items.index')
            ->with('success', 'Barang berhasil dihapus!');
    }

    // ==================== 🔥 GENERATE PDF STIKER ====================
    public function generatePdf(Item $item)
    {
        $pdf = PDF::loadView('admin.items.pdf', compact('item'));
        
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('stiker-' . $item->code . '.pdf');
    }
}