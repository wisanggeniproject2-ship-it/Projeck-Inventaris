<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;

class QRCodeService
{
    public function generateQrCode(Item $item)
    {
        // Data yang akan di-encode ke QR Code
        $data = [
            'id' => $item->id,
            'code' => $item->code,
            'name' => $item->name,
            'unit' => $item->unit->name,
            'location' => $item->location,
        ];

        // Generate QR Code dalam format SVG (tidak butuh imagick)
        $qrCode = QrCode::format('svg')
            ->size(300)
            ->margin(10)
            ->errorCorrection('H')
            ->generate(json_encode($data));
        
        $filename = 'qrcodes/' . $item->code . '.svg';
        Storage::disk('public')->put($filename, $qrCode);
        
        return $filename;
    }

    // Untuk scan QR Code
    public function decodeQrCode($qrCodeData)
    {
        return json_decode($qrCodeData, true);
    }
}