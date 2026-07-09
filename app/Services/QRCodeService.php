<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;

class QRCodeService
{
    public function generateQrCode(Item $item)
    {
        // ✅ BUAT TEKS INFORMASI BARANG (BUKAN URL / JSON)
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

        // Generate QR Code dari TEKS
        $qrCode = QrCode::format('svg')
            ->size(300)
            ->margin(10)
            ->errorCorrection('H')
            ->generate($teks);  // ← QR berisi TEKS!
        
        $filename = 'qrcodes/' . $item->code . '.svg';
        Storage::disk('public')->put($filename, $qrCode);
        
        return $filename;
    }
}