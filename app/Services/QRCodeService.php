<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;

class QRCodeService
{
    public function generateQrCode(Item $item)
    {
        // ✅ TEKS INFORMASI LENGKAP BARANG (sesuai permintaan)
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

        // ✅ FORMAT TETAP PNG (dibaca GD untuk PDF & Export PNG) — pakai endroid/qr-code, tidak butuh Imagick
        $qrCode = QrCode::create($teks)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setSize(400)
            ->setMargin(10)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // ✅ NAMA FILE DIBERSIHKAN DARI TANDA "/" (WAJIB, supaya tidak error)
        $cleanCode = str_replace('/', '-', $item->code);
        $filename  = 'qrcodes/' . $cleanCode . '.png';

        Storage::disk('public')->put($filename, $result->getString());

        return $filename;
    }
}