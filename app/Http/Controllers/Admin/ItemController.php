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
            ->with('success', 'Barang berhasil ditambahkan!');
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
        $cleanCode = str_replace('/', '-', $item->code);
        $pdf = Pdf::loadView('admin.items.pdf', compact('item'))->setPaper('a4', 'portrait');
        return $pdf->download('barang-' . $cleanCode . '.pdf');
    }

    // ==================== 🔥 GENERATE PNG STIKER ====================
    public function generatePng(Item $item)
    {
        $item->load(['category', 'unit', 'fundingSource']);

        $fontRegular = public_path('fonts/font-regular.ttf');
        $fontBold    = public_path('fonts/font-bold.ttf');

        $width  = 920;
        $height = 290;

        $col1End = 180;  
        $col2Start = 180; $col2End = 720;  
        $col3Start = 720; $col3End = 920;  

        $canvas = imagecreatetruecolor($width, $height);
        $white  = imagecolorallocate($canvas, 255, 255, 255);
        $teal   = imagecolorallocate($canvas, 0, 121, 107);
        $gray   = imagecolorallocate($canvas, 153, 153, 153);
        $dark   = imagecolorallocate($canvas, 34, 34, 34);
        imagefill($canvas, 0, 0, $white);

        imagesetthickness($canvas, 4);
        imagerectangle($canvas, 2, 2, $width - 3, $height - 3, $teal);

        imageline($canvas, $col1End, 0, $col1End, $height, $teal);
        imageline($canvas, $col2End, 0, $col2End, $height, $teal);

        imagesetthickness($canvas, 2);
        imageline($canvas, $col2Start, 85, $col2End, 85, $teal);
        imagesetthickness($canvas, 1);
        imageline($canvas, $col2Start, 150, $col2End, 150, $teal);
        imageline($canvas, $col2Start, 220, $col2End, 220, $teal);

        $logoPath = public_path('images/logopermata.png');
        if (file_exists($logoPath)) {
            $logoTargetWidth = 120;
            $imageInfo = getimagesize($logoPath);
            if ($imageInfo) {
                $logoH = (int) round($logoTargetWidth * ($imageInfo[1] / $imageInfo[0]));
                $logoX = (int) (($col1End - $logoTargetWidth) / 2);
                $logoY = (int) (($height - $logoH) / 2);
                
                $source = match ($imageInfo['mime']) {
                    'image/png'  => imagecreatefrompng($logoPath),
                    'image/jpeg' => imagecreatefromjpeg($logoPath),
                    default      => null,
                };
                if ($source) {
                    imagealphablending($canvas, true);
                    imagesavealpha($canvas, true);
                    imagecopyresampled($canvas, $source, $logoX, $logoY, 0, 0, $logoTargetWidth, $logoH, imagesx($source), imagesy($source));
                    imagedestroy($source);
                }
            }
        }

        imagettftext($canvas, 18, 0, 200, 45, $teal, $fontBold, 'BARANG INVENTARIS');
        imagettftext($canvas, 10, 0, 200, 68, $gray, $fontRegular, 'MILIK SIT PERMATA MOJOKERTO');

        imagettftext($canvas, 8, 0, 200, 105, $gray, $fontBold, 'KODE');
        imagettftext($canvas, 15, 0, 200, 130, $dark, $fontBold, $item->code);

        imagettftext($canvas, 8, 0, 460, 105, $gray, $fontBold, 'TANGGAL');
        $tanggal = $item->purchase_date ? \Carbon\Carbon::parse($item->purchase_date)->translatedFormat('d/m/Y') : '-';
        imagettftext($canvas, 15, 0, 460, 130, $dark, $fontBold, $tanggal);

        imagettftext($canvas, 8, 0, 200, 175, $gray, $fontBold, 'NAMA BARANG');
        imagettftext($canvas, 15, 0, 200, 200, $dark, $fontBold, $item->name);

        imagettftext($canvas, 8, 0, 200, 245, $gray, $fontBold, 'SUMBER DANA');
        imagettftext($canvas, 15, 0, 200, 270, $dark, $fontBold, $item->fundingSource->name ?? '-');

        $col3CenterX = $col3Start + (($col3End - $col3Start) / 2);
        $qrTargetWidth = 160;

        $labelBaselineY = (int) ((($height - (12 + 12 + $qrTargetWidth + 12 + 12)) / 2) + 12);
        $qrTopY         = $labelBaselineY + 12;
        $codeBaselineY  = $qrTopY + $qrTargetWidth + 12 + 12;

        $bbox1 = imagettfbbox(9, 0, $fontBold, 'SCAN ME');
        imagettftext($canvas, 9, 0, (int) ($col3CenterX - (abs($bbox1[4] - $bbox1[0]) / 2)), $labelBaselineY, $teal, $fontBold, 'SCAN ME');

        // 🔥 FORMAT TEKS BARU: Menyamakan persis seperti struktur QRCodeService
        $teksScan = "==================================\n";
        $teksScan .= "      BARANG INVENTARIS\n";
        $teksScan .= "   SIT PERMATA MOJOKERTO\n";
        $teksScan .= "==================================\n\n";
        $teksScan .= "Nama Barang   : " . $item->name . "\n";
        $teksScan .= "Kode          : " . $item->code . "\n";
        $teksScan .= "Unit          : " . ($item->unit->name ?? '-') . "\n";
        $teksScan .= "Lokasi        : " . ($item->location ?? '-') . "\n";
        $teksScan .= "Kondisi       : " . ucfirst($item->condition ?? '-') . "\n";
        $teksScan .= "Status        : " . ($item->status ?? '-') . "\n";
        $teksScan .= "Sumber Dana   : " . ($item->fundingSource->name ?? '-') . "\n";
        $teksScan .= "Tanggal Beli  : " . ($item->purchase_date ? date('d/m/Y', strtotime($item->purchase_date)) : '-') . "\n";
        $teksScan .= "==================================\n";
        $teksScan .= "Scan pada: " . date('d/m/Y H:i:s') . "\n";

        // Render QR Code langsung dengan Teks Baru
        $matrix = (\BaconQrCode\Encoder\Encoder::encode($teksScan, \BaconQrCode\Common\ErrorCorrectionLevel::M()))->getMatrix();
        $matrixWidth = $matrix->getWidth();
        $qrX = (int) ($col3CenterX - ($qrTargetWidth / 2));
        imagefilledrectangle($canvas, $qrX, $qrTopY, $qrX + $qrTargetWidth, $qrTopY + $qrTargetWidth, $white);
        $moduleSize = $qrTargetWidth / $matrixWidth;
        
        for ($row = 0; $row < $matrixWidth; $row++) {
            for ($col = 0; $col < $matrixWidth; $col++) {
                if ($matrix->get($col, $row) == 1) {
                    imagefilledrectangle($canvas, $qrX + (int)round($col * $moduleSize), $qrTopY + (int)round($row * $moduleSize), $qrX + (int)round(($col + 1) * $moduleSize) - 1, $qrTopY + (int)round(($row + 1) * $moduleSize) - 1, $dark);
                }
            }
        }

        $bbox2 = imagettfbbox(9, 0, $fontBold, $item->code);
        imagettftext($canvas, 9, 0, (int) ($col3CenterX - (abs($bbox2[4] - $bbox2[0]) / 2)), $codeBaselineY, $teal, $fontBold, $item->code);

        ob_start();
        imagepng($canvas);
        $imageData = ob_get_clean();
        imagedestroy($canvas);

        $cleanCode = str_replace('/', '-', $item->code);
        return response($imageData)->header('Content-Type', 'image/png')->header('Content-Disposition', 'attachment; filename="stiker-' . $cleanCode . '.png"');
    }
}