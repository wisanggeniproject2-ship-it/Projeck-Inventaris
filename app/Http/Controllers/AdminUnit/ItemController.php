<?php

namespace App\Http\Controllers\AdminUnit;

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
        $unitId = auth()->user()->unit_id;
        
        $query = Item::with(['category', 'unit', 'fundingSource'])->where('unit_id', $unitId);
        
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

    // ==================== CREATE ====================
    public function create()
    {
        $categories = Category::all();
        $units = Unit::where('id', auth()->user()->unit_id)->get();
        $fundingSources = FundingSource::where('is_active', true)->get();
        return view('admin_unit.items.create', compact('categories', 'units', 'fundingSources'));
    }

    // ==================== STORE ====================
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
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
            auth()->user()->unit_id,
            $request->category_id,
            $request->purchase_date ? \Carbon\Carbon::parse($request->purchase_date) : null
        );

        $data = [
            'code' => $code,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'unit_id' => auth()->user()->unit_id,
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

        // 🔥 REDIRECT KE admin_unit.items.index (BUKAN super_admin)
        return redirect()->route('admin_unit.items.index')
            ->with('success', 'Barang berhasil ditambahkan! Kode: ' . $code . ' | Stok: ' . $request->stock);
    }

    // ==================== SHOW ====================
    public function show(Item $item)
    {
        if ($item->unit_id !== auth()->user()->unit_id) {
            abort(403);
        }
        
        $item->load(['category', 'unit', 'fundingSource', 'circulations.user']);
        return view('admin_unit.items.show', compact('item'));
    }

    // ==================== EDIT ====================
    public function edit(Item $item)
    {
        abort(403, 'Admin unit tidak memiliki akses untuk mengedit barang.');
    }

    // ==================== UPDATE ====================
    public function update(Request $request, Item $item)
    {
        abort(403, 'Admin unit tidak memiliki akses untuk mengupdate barang.');
    }

    // ==================== DESTROY ====================
    public function destroy(Item $item)
    {
        abort(403, 'Admin unit tidak memiliki akses untuk menghapus barang.');
    }

   // ==================== GENERATE PDF STIKER ====================
public function generatePdf(Item $item)
{
    // Cek akses: hanya admin unit yang memiliki unit yang sama
    if ($item->unit_id !== auth()->user()->unit_id) {
        abort(403, 'Anda tidak memiliki akses ke barang ini.');
    }

    $item->load(['category', 'unit', 'fundingSource']);
    
    // 🔥 PAKAI VIEW YANG SAMA DENGAN SUPER ADMIN
    $pdf = Pdf::loadView('admin.items.pdf', compact('item'));
    
    // PAKAI A4 LANDSCAPE
    $pdf->setPaper('A4', 'landscape');
    
    // Bersihkan nama file
    $cleanCode = str_replace('/', '-', $item->code);
    $fileName = 'stiker-' . $cleanCode . '.pdf';
    
    return $pdf->download($fileName);
}
}