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
        if ($item->unit_id !== auth()->user()->unit_id) {
            abort(403, 'Anda tidak memiliki akses ke barang ini.');
        }

        $item->load(['category', 'unit', 'fundingSource']);

        $pdf = Pdf::loadView('admin.items.pdf', compact('item'));
        $pdf->setPaper('A4', 'landscape');

        $cleanCode = str_replace('/', '-', $item->code);
        $fileName = 'stiker-' . $cleanCode . '.pdf';

        return $pdf->download($fileName);
    }

    // ==================== GENERATE PNG STIKER ====================
    public function generatePng(Item $item)
    {
        if ($item->unit_id !== auth()->user()->unit_id) {
            abort(403, 'Anda tidak memiliki akses ke barang ini.');
        }

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
            $logoSize = $this->getScaledSize($logoPath, $logoTargetWidth);
            if ($logoSize) {
                $logoX = (int) (($col1End - $logoSize[0]) / 2);
                $logoY = (int) (($height - $logoSize[1]) / 2);
                $this->placeImageOnCanvas($canvas, $logoPath, $logoX, $logoY, $logoTargetWidth);
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

        $totalBlockHeight = 12 + 12 + $qrTargetWidth + 12 + 12;
        $blockStartY = (int) (($height - $totalBlockHeight) / 2);

        $labelBaselineY = $blockStartY + 12;
        $qrTopY         = $labelBaselineY + 12;
        $codeBaselineY  = $qrTopY + $qrTargetWidth + 12 + 12;

        $this->centeredText($canvas, 9, $col3CenterX, $labelBaselineY, $teal, $fontBold, 'SCAN ME');

        $qrX = (int) ($col3CenterX - ($qrTargetWidth / 2));
        $this->drawQrOnCanvas($canvas, $this->buildQrText($item), $qrX, $qrTopY, $qrTargetWidth);

        $this->centeredText($canvas, 9, $col3CenterX, $codeBaselineY, $teal, $fontBold, $item->code);

        ob_start();
        imagepng($canvas);
        $imageData = ob_get_clean();
        imagedestroy($canvas);

        $cleanCode = str_replace('/', '-', $item->code);
        $fileName  = 'stiker-' . $cleanCode . '.png';

        return response($imageData)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition' , 'attachment; filename="' . $fileName . '"');
    }

    private function getScaledSize(string $imagePath, int $targetWidth): ?array
    {
        $imageInfo = getimagesize($imagePath);
        if (!$imageInfo) return null;
        return [$targetWidth, (int) round($targetWidth * ($imageInfo[1] / $imageInfo[0]))];
    }

    private function placeImageOnCanvas($canvas, string $imagePath, int $x, int $y, int $targetWidth): void
    {
        $imageInfo = getimagesize($imagePath);
        if (!$imageInfo) return;
        $source = match ($imageInfo['mime']) {
            'image/png'  => imagecreatefrompng($imagePath),
            'image/jpeg' => imagecreatefromjpeg($imagePath),
            'image/gif'  => imagecreatefromgif($imagePath),
            default      => null,
        };
        if (!$source) return;
        imagealphablending($canvas, true);
        imagesavealpha($canvas, true);
        imagecopyresampled($canvas, $source, $x, $y, 0, 0, $targetWidth, (int) round($targetWidth * (imagesy($source) / imagesx($source))), imagesx($source), imagesy($source));
        imagedestroy($source);
    }

    private function centeredText($canvas, int $size, int $centerX, int $baselineY, $color, string $fontPath, string $text): void
    {
        $bbox = imagettfbbox($size, 0, $fontPath, $text);
        imagettftext($canvas, $size, 0, (int) ($centerX - (abs($bbox[4] - $bbox[0]) / 2)), $baselineY, $color, $fontPath, $text);
    }

    // 🔥 PERBAIKAN: Menyamakan persis output teks scan seperti di QRCodeService
    private function buildQrText(Item $item): string
    {
        $teks = "==================================\n";
        $teks .= "      BARANG INVENTARIS\n";
        $teks .= "   SIT PERMATA MOJOKERTO\n";
        $teks .= "==================================\n\n";
        $teks .= "Nama Barang   : " . $item->name . "\n";
        $teks .= "Kode          : " . $item->code . "\n";
        $teks .= "Unit          : " . ($item->unit->name ?? '-') . "\n";
        $teks .= "Lokasi        : " . ($item->location ?? '-') . "\n";
        $teks .= "Kondisi       : " . ucfirst($item->condition ?? '-') . "\n";
        $teks .= "Status        : " . ($item->status ?? '-') . "\n";
        $teks .= "Sumber Dana   : " . ($item->fundingSource->name ?? '-') . "\n";
        $teks .= "Tanggal Beli  : " . ($item->purchase_date ? date('d/m/Y', strtotime($item->purchase_date)) : '-') . "\n";
        $teks .= "==================================\n";
        $teks .= "Scan pada: " . date('d/m/Y H:i:s') . "\n";

        return $teks;
    }

    private function drawQrOnCanvas($canvas, string $text, int $x, int $y, int $targetSize): void
    {
        $matrix = (\BaconQrCode\Encoder\Encoder::encode($text, \BaconQrCode\Common\ErrorCorrectionLevel::M())) ->getMatrix();
        $matrixWidth = $matrix->getWidth();
        $black = imagecolorallocate($canvas, 0, 0, 0);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefilledrectangle($canvas, $x, $y, $x + $targetSize, $y + $targetSize, $white);
        $moduleSize = $targetSize / $matrixWidth;
        for ($row = 0; $row < $matrixWidth; $row++) {
            for ($col = 0; $col < $matrixWidth; $col++) {
                if ($matrix->get($col, $row) == 1) {
                    imagefilledrectangle($canvas, $x + (int)round($col * $moduleSize), $y + (int)round($row * $moduleSize), $x + (int)round(($col + 1) * $moduleSize) - 1, $y + (int)round(($row + 1) * $moduleSize) - 1, $black);
                }
            }
        }
    }
}