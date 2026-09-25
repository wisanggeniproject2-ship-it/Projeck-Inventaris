<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stiker Barang - {{ $item->code }}</title>
    <style>
        /* ============================================================ */
        /*  RESET & PAGE SETUP                                          */
        /* ============================================================ */
        @page {
            size: A4 portrait;
            margin: 6mm 5mm 6mm 5mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 8px;
            color: #222;
            background: #fff;
        }

        /* ============================================================ */
        /*  GRID 2 KOLOM × 5 BARIS = 10 STIKER PER HALAMAN              */
        /* ============================================================ */
        .sticker-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .sticker-row {
            page-break-inside: avoid;
        }

        .sticker-cell {
            width: 50%;
            padding: 0 1.5mm 3mm 1.5mm;
            vertical-align: top;
        }

        .sticker-cell:first-child {
            padding-left: 0;
        }

        .sticker-cell:last-child {
            padding-right: 0;
        }

        /* ============================================================ */
        /*  KARTU STIKER                                                */
        /* ============================================================ */
        .sticker {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #00796B;
            background: #fff;
        }

        .col-logo { width: 20%; }
        .col-info { width: 48%; }
        .col-qr   { width: 32%; }

        /* ===== KOLOM 1: LOGO ===== */
        .cell-logo {
            text-align: center;
            vertical-align: middle;
            padding: 8px 4px;
            border-right: 1.5px solid #00796B;
            background: #f8fdfc;
        }

        .cell-logo img {
            width: 55px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .logo-fallback {
            width: 55px;
            height: 40px;
            margin: 0 auto;
            border: 2px solid #00796B;
            display: inline-block;
        }

        /* ===== KOLOM 2: INFO ===== */
        .cell-info-wrap {
            padding: 0;
            border-right: 1.5px solid #00796B;
            vertical-align: top;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 4px 6px;
            border-bottom: 1px solid #b2dfdb;
            vertical-align: top;
        }

        .info-table tr:last-child td {
            border-bottom: none;
        }

        .title-text {
            font-size: 9px;
            font-weight: 700;
            color: #00796B;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1.1;
            display: block;
        }

        .subtitle-text {
            font-size: 6px;
            font-weight: 400;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            display: block;
            margin-top: 1px;
        }

        .row-label {
            font-size: 5.5px;
            font-weight: 700;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            display: block;
            margin-bottom: 0.5px;
        }

        .row-value {
            font-size: 7.5px;
            font-weight: 700;
            color: #222;
            display: block;
            line-height: 1.15;
        }

        .td-kode    { width: 58%; }
        .td-tanggal { width: 42%; }

        .row-value-code {
            font-family: 'Courier New', monospace;
            font-size: 6.5px;
            word-break: break-all;
            line-height: 1.1;
        }

        /* ===== KOLOM 3: QR ===== */
        .cell-qr {
            text-align: center;
            vertical-align: middle;
            padding: 6px 4px;
            background: #f8fdfc;
        }

        .scan-label {
            display: block;
            font-size: 5.5px;
            font-weight: 700;
            color: #00796B;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 2px;
        }

        .qr-image {
            width: 55px;
            height: 55px;
            display: block;
            margin: 0 auto;
        }

        .qr-placeholder {
            width: 55px;
            height: 55px;
            margin: 0 auto;
            border: 1px solid #ddd;
            background: #f9f9f9;
            line-height: 55px;
            font-size: 9px;
            color: #999;
        }

        .qr-code-value {
            display: block;
            margin-top: 3px;
            font-family: 'Courier New', monospace;
            font-size: 5.5px;
            font-weight: 700;
            color: #00796B;
            letter-spacing: 0.2px;
            background: #E0F2F1;
            padding: 1.5px 3px;
            word-break: break-all;
            line-height: 1.05;
            border-radius: 2px;
        }

        /* ============================================================ */
        /*  HEADER & FOOTER (info halaman)                              */
        /* ============================================================ */
        .page-info {
            text-align: center;
            font-size: 7px;
            color: #666;
            margin-bottom: 3mm;
            padding-bottom: 1mm;
            border-bottom: 1px solid #ddd;
        }

        .page-info strong {
            color: #00796B;
        }

        /* Page break — untuk multi-halaman */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

@php
    /**
     * 🔥 Generate QR per kode stok
     */
    if (!function_exists('sticker_generate_qr_data_uri')) {
        function sticker_generate_qr_data_uri(string $text, int $size = 240): ?string
        {
            try {
                $qrCode = \BaconQrCode\Encoder\Encoder::encode(
                    $text,
                    \BaconQrCode\Common\ErrorCorrectionLevel::M()
                );
                $matrix = $qrCode->getMatrix();
                $matrixWidth = $matrix->getWidth();

                $img = imagecreatetruecolor($size, $size);
                $white = imagecolorallocate($img, 255, 255, 255);
                $black = imagecolorallocate($img, 0, 0, 0);
                imagefilledrectangle($img, 0, 0, $size, $size, $white);

                $moduleSize = $size / $matrixWidth;

                for ($row = 0; $row < $matrixWidth; $row++) {
                    for ($col = 0; $col < $matrixWidth; $col++) {
                        if ($matrix->get($col, $row) == 1) {
                            $x1 = (int) round($col * $moduleSize);
                            $x2 = (int) round(($col + 1) * $moduleSize);
                            $y1 = (int) round($row * $moduleSize);
                            $y2 = (int) round(($row + 1) * $moduleSize);
                            imagefilledrectangle($img, $x1, $y1, $x2 - 1, $y2 - 1, $black);
                        }
                    }
                }

                ob_start();
                imagepng($img);
                $data = ob_get_clean();
                imagedestroy($img);

                return 'data:image/png;base64,' . base64_encode($data);
            } catch (\Exception $e) {
                \Log::warning('Gagal generate QR untuk stiker PDF: ' . $e->getMessage());
                return null;
            }
        }
    }

    /**
     * 🔥 Generate QR text per kode stok spesifik
     */
    if (!function_exists('sticker_build_qr_text')) {
        function sticker_build_qr_text($item, $stockCode): string
        {
            $text  = "==================================\n";
            $text .= "      BARANG INVENTARIS\n";
            $text .= "   SIT PERMATA MOJOKERTO\n";
            $text .= "==================================\n\n";
            $text .= "Nama Barang   : " . $item->name . "\n";
            $text .= "Kode          : " . $stockCode . "\n";
            $text .= "Unit          : " . ($item->unit->name ?? '-') . "\n";
            $text .= "Lokasi        : " . ($item->location ?? '-') . "\n";
            $text .= "Kondisi       : " . ucfirst($item->condition ?? '-') . "\n";
            $text .= "Sumber Dana   : " . ($item->fundingSource->name ?? '-') . "\n";
            $text .= "Tanggal Beli  : " . ($item->purchase_date
                ? \Carbon\Carbon::parse($item->purchase_date)->translatedFormat('d/m/Y')
                : '-') . "\n";
            $text .= "==================================\n";
            $text .= "Scan pada: " . date('d/m/Y H:i:s') . "\n";

            return $text;
        }
    }

    // 🔥 Logo
    $logoPath   = public_path('images/logopermata.png');
    $logoExists = file_exists($logoPath);
    $logoDataUri = null;
    if ($logoExists) {
        $logoDataUri = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }

    // 🔥 Tanggal
    $tanggalBeli = $item->purchase_date
        ? \Carbon\Carbon::parse($item->purchase_date)->translatedFormat('d/m/Y')
        : '-';
@endphp

{{-- ============================================================ --}}
{{-- INFO HALAMAN (opsional — kalau ada $pageNumber & $totalPages) --}}
{{-- ============================================================ --}}
@if(isset($pageNumber) && isset($totalPages))
<div class="page-info">
    <strong>STIKER INVENTARIS</strong> &mdash; {{ $item->name }}
    &nbsp;|&nbsp;
    Halaman <strong>{{ $pageNumber }}</strong> dari <strong>{{ $totalPages }}</strong>
    &nbsp;|&nbsp;
    Total <strong>{{ $stockCodes->count() }}</strong> stiker
</div>
@endif

{{-- ============================================================ --}}
{{-- GRID 10 STIKER (2 KOLOM × 5 BARIS)                          --}}
{{-- ============================================================ --}}
<table class="sticker-grid">
    @foreach($stockCodes->chunk(2) as $rowIndex => $row)
    <tr class="sticker-row">
        @foreach($row as $stock)
            @php
                // 🔥 QR per kode stok
                $qrText    = sticker_build_qr_text($item, $stock->stock_code);
                $qrDataUri = sticker_generate_qr_data_uri($qrText, 240);
            @endphp

            <td class="sticker-cell">
                <table class="sticker">
                    <colgroup>
                        <col class="col-logo">
                        <col class="col-info">
                        <col class="col-qr">
                    </colgroup>
                    <tr>
                        {{-- KOLOM 1: LOGO --}}
                        <td class="cell-logo">
                            @if($logoDataUri)
                                <img src="{{ $logoDataUri }}" alt="Logo">
                            @else
                                <span class="logo-fallback"></span>
                            @endif
                        </td>

                        {{-- KOLOM 2: INFO --}}
                        <td class="cell-info-wrap">
                            <table class="info-table">
                                <tr>
                                    <td colspan="2">
                                        <span class="title-text">Barang Inventaris</span>
                                        <span class="subtitle-text">Milik SIT Permata Mojokerto</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="td-kode">
                                        <span class="row-label">Kode Stok</span>
                                        <span class="row-value row-value-code">{{ $stock->stock_code }}</span>
                                    </td>
                                    <td class="td-tanggal">
                                        <span class="row-label">Tanggal</span>
                                        <span class="row-value">{{ $tanggalBeli }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <span class="row-label">Nama Barang</span>
                                        <span class="row-value">{{ $item->name }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <span class="row-label">Sumber Dana</span>
                                        <span class="row-value">{{ $item->fundingSource->name ?? '-' }}</span>
                                    </td>
                                </tr>
                            </table>
                        </td>

                        {{-- KOLOM 3: QR --}}
                        <td class="cell-qr">
                            <span class="scan-label">Scan Me</span>
                            @if($qrDataUri)
                                <img src="{{ $qrDataUri }}" alt="QR Code" class="qr-image" width="55" height="55">
                            @else
                                <div class="qr-placeholder">QR</div>
                            @endif
                            <span class="qr-code-value">{{ $stock->stock_code }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        @endforeach

        {{-- Kalau baris cuma 1 stiker, tambah cell kosong biar tetap 2 kolom --}}
        @if($row->count() === 1)
            <td class="sticker-cell">
                {{-- spacer --}}
            </td>
        @endif
    </tr>
    @endforeach
</table>

</body>
</html>