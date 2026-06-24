<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;

class QRCodeService
{
    public function generateQrCode(Item $item)
    {
        $data = [
            'id' => $item->id,
            'code' => $item->code,
            'name' => $item->name,
            'location' => $item->location,
            'budget_source' => $item->budget_source,
        ];

        // VERSI TERBARU: Pakai SVG (TIDAK BUTUH IMAGICK)
        $qrCode = QrCode::format('svg')
            ->size(300)
            ->margin(10)
            ->errorCorrection('H')
            ->generate(json_encode($data));
        
        $filename = 'qrcodes/' . $item->code . '.svg';
        Storage::disk('public')->put($filename, $qrCode);
        
        return $filename;
    }
}