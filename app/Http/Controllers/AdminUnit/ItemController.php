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

        $item->generateFullCode();
        $item->syncStockCodes();

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

        $item->load(['category', 'unit', 'fundingSource', 'circulations.user', 'stockCodes']);
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

    // ============================================================
    // 🔥🔥🔥 GENERATE PDF STIKER — SPLIT 10 STIKER PER FILE
    // ============================================================
    public function generatePdf(Item $item)
    {
        // 🔥 Cek akses admin unit
        if ($item->unit_id !== auth()->user()->unit_id) {
            abort(403, 'Anda tidak memiliki akses ke barang ini.');
        }

        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', '600');

        $item->load(['category', 'unit', 'fundingSource']);

        // 🔥 Ambil SEMUA kode stok AKTIF (available + borrowed + maintenance)
        // Disposed TIDAK dicetak (barang sudah hilang)
        $stockCodes = $item->stockCodes()
            ->where('status', '!=', 'disposed')
            ->orderBy('stock_number')
            ->get();

        // Fallback kalau item_stocks kosong (item lama)
        if ($stockCodes->count() === 0) {
            $stockCodes = collect([
                (object) [
                    'stock_number' => 1,
                    'stock_code'   => $item->full_code ?? $item->code,
                    'status'       => 'available',
                ],
            ]);
        }

        $totalStok = $stockCodes->count();
        $perPage   = 10;  // 🔥 10 stiker per halaman A4
        $cleanCode = str_replace(['/', '.'], '-', $item->code);

        // ============================================================
        // KASUS 1: 1-10 stok → 1 file PDF biasa
        // ============================================================
        if ($totalStok <= $perPage) {
            $pdf = Pdf::loadView('admin.items.pdf', [
                'item'       => $item,
                'stockCodes' => $stockCodes,
                'pageNumber' => 1,
                'totalPages' => 1,
            ])->setPaper('a4', 'portrait');

            return $pdf->download('stiker-' . $cleanCode . '-x' . $totalStok . '.pdf');
        }

        // ============================================================
        // KASUS 2: > 10 stok → ZIP berisi multiple PDF (10 stiker/file)
        // ============================================================
        $chunks     = $stockCodes->chunk($perPage);
        $totalPages = $chunks->count();

        // Buat ZIP temporary
        $zipFileName = tempnam(sys_get_temp_dir(), 'stiker_pdf_') . '.zip';
        $zip = new \ZipArchive();

        if ($zip->open($zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat ZIP file');
        }

        foreach ($chunks as $pageIndex => $chunk) {
            $pageNum = $pageIndex + 1;

            // Render PDF per batch
            $pdf = Pdf::loadView('admin.items.pdf', [
                'item'       => $item,
                'stockCodes' => $chunk,
                'pageNumber' => $pageNum,
                'totalPages' => $totalPages,
            ])->setPaper('a4', 'portrait');

            $pdfContent = $pdf->output();

            // Range nomor stok
            $firstNo = $chunk->first()->stock_number ?? 1;
            $lastNo  = $chunk->last()->stock_number ?? 1;

            $pageNumStr = str_pad($pageNum, 2, '0', STR_PAD_LEFT);
            $totalStr   = str_pad($totalPages, 2, '0', STR_PAD_LEFT);

            $fileName = "stiker-{$cleanCode}-page{$pageNumStr}of{$totalStr}-stok{$firstNo}sd{$lastNo}.pdf";

            $zip->addFromString($fileName, $pdfContent);
        }

        $zip->close();

        $zipDownloadName = 'stiker-' . $cleanCode . '-x' . $totalStok . '.zip';

        return response()->download($zipFileName, $zipDownloadName, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    // ============================================================
    // 🔥🔥🔥 GENERATE PNG STIKER — SEMUA KODE AKTIF
    // ============================================================
    public function generatePng(Item $item)
    {
        // 🔥 Cek akses admin unit
        if ($item->unit_id !== auth()->user()->unit_id) {
            abort(403, 'Anda tidak memiliki akses ke barang ini.');
        }

        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', '600');

        $item->load(['category', 'unit', 'fundingSource']);

        // 🔥 Ambil SEMUA kode stok AKTIF (available + borrowed + maintenance)
        $stockCodes = $item->stockCodes()
            ->where('status', '!=', 'disposed')
            ->orderBy('stock_number')
            ->get()
            ->map(fn($s) => [
                'id'     => $s->id,
                'no'     => $s->stock_number,
                'code'   => $s->stock_code,
                'status' => $s->status,
            ])
            ->toArray();

        $totalStok = count($stockCodes);

        if ($totalStok === 0) {
            $stockCodes = [
                ['no' => 1, 'code' => $item->full_code ?? $item->code, 'status' => 'available'],
            ];
            $totalStok = 1;
        }

        $maxPerFile = 25;

        if ($totalStok <= $maxPerFile) {
            return $this->generateSinglePngFile($item, $stockCodes, $totalStok);
        }

        return $this->generateMultiPngZip($item, $stockCodes, $totalStok, $maxPerFile);
    }

    private function generateSinglePngFile(Item $item, array $stockCodes, int $totalStok)
    {
        $fontRegular = public_path('fonts/font-regular.ttf');
        $fontBold    = public_path('fonts/font-bold.ttf');

        $stickerW = 920;
        $stickerH = 290;
        $gapX = 30;
        $gapY = 30;
        $padOuter = 30;

        $cols = 2;
        $rows = (int) ceil($totalStok / $cols);

        $width  = ($stickerW * $cols) + ($gapX * ($cols - 1)) + ($padOuter * 2);
        $height = ($stickerH * $rows) + ($gapY * ($rows - 1)) + ($padOuter * 2);

        $canvas = imagecreatetruecolor($width, $height);
        $bgGray = imagecolorallocate($canvas, 245, 245, 245);
        imagefill($canvas, 0, 0, $bgGray);

        $logoData = $this->loadLogoData();

        foreach ($stockCodes as $index => $sc) {
            $col = $index % $cols;
            $row = (int) floor($index / $cols);

            $offsetX = $padOuter + ($col * ($stickerW + $gapX));
            $offsetY = $padOuter + ($row * ($stickerH + $gapY));

            $this->drawSticker(
                $canvas, $item, $sc,
                $offsetX, $offsetY,
                $stickerW, $stickerH,
                $fontRegular, $fontBold,
                $logoData
            );
        }

        ob_start();
        imagepng($canvas, null, 6);
        $imageData = ob_get_clean();
        imagedestroy($canvas);

        $cleanCode = str_replace(['/', '.'], '-', $item->code);
        $fileName  = 'stiker-' . $cleanCode . '-x' . $totalStok . '.png';

        return response($imageData)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    private function generateMultiPngZip(Item $item, array $stockCodes, int $totalStok, int $maxPerFile)
    {
        $fontRegular = public_path('fonts/font-regular.ttf');
        $fontBold    = public_path('fonts/font-bold.ttf');

        $stickerW = 920;
        $stickerH = 290;
        $gapX = 30;
        $gapY = 30;
        $padOuter = 30;
        $cols = 2;

        $chunks     = array_chunk($stockCodes, $maxPerFile);
        $totalBatch = count($chunks);

        $logoData = $this->loadLogoData();

        $zipFileName = tempnam(sys_get_temp_dir(), 'stiker_') . '.zip';
        $zip = new \ZipArchive();

        if ($zip->open($zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat ZIP file');
        }

        $cleanCode = str_replace(['/', '.'], '-', $item->code);

        foreach ($chunks as $batchIndex => $batch) {
            $batchNum   = $batchIndex + 1;
            $batchCount = count($batch);

            $rows = (int) ceil($batchCount / $cols);

            $width  = ($stickerW * $cols) + ($gapX * ($cols - 1)) + ($padOuter * 2);
            $height = ($stickerH * $rows) + ($gapY * ($rows - 1)) + ($padOuter * 2);

            $canvas = imagecreatetruecolor($width, $height);
            $bgGray = imagecolorallocate($canvas, 245, 245, 245);
            imagefill($canvas, 0, 0, $bgGray);

            foreach ($batch as $index => $sc) {
                $col = $index % $cols;
                $row = (int) floor($index / $cols);

                $offsetX = $padOuter + ($col * ($stickerW + $gapX));
                $offsetY = $padOuter + ($row * ($stickerH + $gapY));

                $this->drawSticker(
                    $canvas, $item, $sc,
                    $offsetX, $offsetY,
                    $stickerW, $stickerH,
                    $fontRegular, $fontBold,
                    $logoData
                );
            }

            ob_start();
            imagepng($canvas, null, 6);
            $imageData = ob_get_clean();
            imagedestroy($canvas);

            $stikerNum = str_pad($batchNum, 3, '0', STR_PAD_LEFT);
            $totalNum  = str_pad($totalBatch, 3, '0', STR_PAD_LEFT);
            $fileName  = "stiker-{$cleanCode}-part{$stikerNum}-of{$totalNum}-x{$batchCount}.png";

            $zip->addFromString($fileName, $imageData);
        }

        $zip->close();

        $zipDownloadName = 'stiker-' . $cleanCode . '-x' . $totalStok . '.zip';

        return response()->download($zipFileName, $zipDownloadName, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    private function loadLogoData(): ?array
    {
        $logoPath = public_path('images/logopermata.png');
        if (!file_exists($logoPath)) return null;

        $imageInfo = getimagesize($logoPath);
        if (!$imageInfo) return null;

        $source = match ($imageInfo['mime']) {
            'image/png'  => imagecreatefrompng($logoPath),
            'image/jpeg' => imagecreatefromjpeg($logoPath),
            default      => null,
        };
        if (!$source) return null;

        return [
            'resource' => $source,
            'width'    => imagesx($source),
            'height'   => imagesy($source),
        ];
    }

    private function drawSticker($canvas, Item $item, array $stockCode, int $offsetX, int $offsetY, int $stickerW, int $stickerH, string $fontRegular, string $fontBold, ?array $logoData): void
    {
        $teal  = imagecolorallocate($canvas, 0, 121, 107);
        $gray  = imagecolorallocate($canvas, 153, 153, 153);
        $dark  = imagecolorallocate($canvas, 34, 34, 34);
        $white = imagecolorallocate($canvas, 255, 255, 255);

        // Background putih
        imagefilledrectangle($canvas, $offsetX, $offsetY, $offsetX + $stickerW, $offsetY + $stickerH, $white);

        // Border sticker
        imagesetthickness($canvas, 4);
        imagerectangle($canvas, $offsetX + 2, $offsetY + 2, $offsetX + $stickerW - 3, $offsetY + $stickerH - 3, $teal);

        // Koordinat kolom
        $col1End   = $offsetX + 180;
        $col2Start = $offsetX + 180;
        $col2End   = $offsetX + 720;
        $col3Start = $offsetX + 720;
        $col3End   = $offsetX + 920;

        // Garis vertikal
        imagesetthickness($canvas, 4);
        imageline($canvas, $col1End, $offsetY, $col1End, $offsetY + $stickerH, $teal);
        imageline($canvas, $col2End, $offsetY, $col2End, $offsetY + $stickerH, $teal);

        // Garis horizontal (kolom 2)
        imagesetthickness($canvas, 2);
        imageline($canvas, $col2Start, $offsetY + 85, $col2End, $offsetY + 85, $teal);
        imagesetthickness($canvas, 1);
        imageline($canvas, $col2Start, $offsetY + 150, $col2End, $offsetY + 150, $teal);
        imageline($canvas, $col2Start, $offsetY + 220, $col2End, $offsetY + 220, $teal);

        // ============ LOGO ============
        if ($logoData) {
            $logoTargetWidth = 120;
            $logoH = (int) round($logoTargetWidth * ($logoData['height'] / $logoData['width']));
            $logoX = (int) ($offsetX + (($col1End - $offsetX - $logoTargetWidth) / 2));
            $logoY = (int) ($offsetY + (($stickerH - $logoH) / 2));

            imagecopyresampled(
                $canvas, $logoData['resource'],
                $logoX, $logoY, 0, 0,
                $logoTargetWidth, $logoH,
                $logoData['width'], $logoData['height']
            );
        }

        // ============ INFO ============
        $infoX = $offsetX + 200;

        // Header
        imagettftext($canvas, 18, 0, $infoX, $offsetY + 45, $teal, $fontBold, 'BARANG INVENTARIS');
        imagettftext($canvas, 10, 0, $infoX, $offsetY + 68, $gray, $fontRegular, 'MILIK SIT PERMATA MOJOKERTO');

        // KODE per stok
        imagettftext($canvas, 8, 0, $infoX, $offsetY + 105, $gray, $fontBold, 'KODE');

        $maxCodeWidth = 250;
        $codeFontSize = 11;
        $minFontSize  = 7;

        while ($codeFontSize > $minFontSize) {
            $bbox = imagettfbbox($codeFontSize, 0, $fontBold, $stockCode['code']);
            $codeWidth = abs($bbox[4] - $bbox[0]);
            if ($codeWidth <= $maxCodeWidth) break;
            $codeFontSize -= 0.5;
        }

        // 🔥 SEMUA WARNA HITAM (konsisten)
        imagettftext($canvas, (int) $codeFontSize, 0, $infoX, $offsetY + 130, $dark, $fontBold, $stockCode['code']);

        // TANGGAL
        $tanggal = $item->purchase_date
            ? \Carbon\Carbon::parse($item->purchase_date)->translatedFormat('d/m/Y')
            : '-';
        imagettftext($canvas, 8, 0, $offsetX + 460, $offsetY + 105, $gray, $fontBold, 'TANGGAL');
        imagettftext($canvas, 15, 0, $offsetX + 460, $offsetY + 130, $dark, $fontBold, $tanggal);

        // NAMA BARANG
        imagettftext($canvas, 8, 0, $infoX, $offsetY + 175, $gray, $fontBold, 'NAMA BARANG');

        $maxNameWidth = 500;
        $nameFontSize = 15;
        while ($nameFontSize > 9) {
            $bbox = imagettfbbox($nameFontSize, 0, $fontBold, $item->name);
            $nameWidth = abs($bbox[4] - $bbox[0]);
            if ($nameWidth <= $maxNameWidth) break;
            $nameFontSize -= 0.5;
        }

        imagettftext($canvas, (int) $nameFontSize, 0, $infoX, $offsetY + 200, $dark, $fontBold, $item->name);

        // SUMBER DANA
        imagettftext($canvas, 8, 0, $infoX, $offsetY + 245, $gray, $fontBold, 'SUMBER DANA');
        imagettftext($canvas, 15, 0, $infoX, $offsetY + 270, $dark, $fontBold, $item->fundingSource->name ?? '-');

        // ============ QR CODE ============
        $col3CenterX = $col3Start + (($col3End - $col3Start) / 2);
        $qrTargetWidth = 160;

        $gapLabelToQr = 12;
        $gapQrToCode  = 40;

        $totalBlockHeight = $gapLabelToQr + 12 + $qrTargetWidth + $gapQrToCode;

        $blockStartY    = (int) ($offsetY + (($stickerH - $totalBlockHeight) / 2));
        $labelBaselineY = $blockStartY;
        $qrTopY         = $labelBaselineY + 12;
        $codeBaselineY  = $qrTopY + $qrTargetWidth + $gapQrToCode;

        // SCAN ME
        $bbox1 = imagettfbbox(9, 0, $fontBold, 'SCAN ME');
        imagettftext($canvas, 9, 0, (int) ($col3CenterX - (abs($bbox1[4] - $bbox1[0]) / 2)), $labelBaselineY, $teal, $fontBold, 'SCAN ME');

        // QR Code
        $qrText = $this->buildQrTextForStock($item, $stockCode['code']);

        $matrix = (\BaconQrCode\Encoder\Encoder::encode(
            $qrText,
            \BaconQrCode\Common\ErrorCorrectionLevel::M()
        ))->getMatrix();

        $matrixWidth = $matrix->getWidth();
        $qrX = (int) ($col3CenterX - ($qrTargetWidth / 2));

        imagefilledrectangle($canvas, $qrX, $qrTopY, $qrX + $qrTargetWidth, $qrTopY + $qrTargetWidth, $white);

        $moduleSize = $qrTargetWidth / $matrixWidth;

        for ($row = 0; $row < $matrixWidth; $row++) {
            for ($col = 0; $col < $matrixWidth; $col++) {
                if ($matrix->get($col, $row) == 1) {
                    imagefilledrectangle(
                        $canvas,
                        $qrX + (int) round($col * $moduleSize),
                        $qrTopY + (int) round($row * $moduleSize),
                        $qrX + (int) round(($col + 1) * $moduleSize) - 1,
                        $qrTopY + (int) round(($row + 1) * $moduleSize) - 1,
                        $dark
                    );
                }
            }
        }

        // KODE DI BAWAH QR
        $maxQrCodeWidth = 195;
        $qrCodeFontSize = 7;
        while ($qrCodeFontSize > 4) {
            $bbox2 = imagettfbbox($qrCodeFontSize, 0, $fontBold, $stockCode['code']);
            $qrCodeWidth = abs($bbox2[4] - $bbox2[0]);
            if ($qrCodeWidth <= $maxQrCodeWidth) break;
            $qrCodeFontSize -= 0.5;
        }

        $bbox2 = imagettfbbox($qrCodeFontSize, 0, $fontBold, $stockCode['code']);
        imagettftext(
            $canvas, $qrCodeFontSize, 0,
            (int) ($col3CenterX - (abs($bbox2[4] - $bbox2[0]) / 2)),
            $codeBaselineY, $teal, $fontBold, $stockCode['code']
        );
    }

    private function buildQrTextForStock(Item $item, string $stockCode): string
    {
        $teks = "==================================\n";
        $teks .= "      BARANG INVENTARIS\n";
        $teks .= "   SIT PERMATA MOJOKERTO\n";
        $teks .= "==================================\n\n";
        $teks .= "Nama Barang   : " . $item->name . "\n";
        $teks .= "Kode          : " . $stockCode . "\n";
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
}