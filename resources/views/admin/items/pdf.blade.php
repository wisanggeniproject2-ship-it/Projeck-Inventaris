<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stiker Barang - {{ $item->code }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #f2f2f2;
            font-family: 'Arial', 'Helvetica', sans-serif;
            padding: 20px 10px;
        }

        /* ===== KARTU STIKER — 1 BARIS, 3 KOLOM. TANPA ROWSPAN. ===== */
        .sticker {
            width: 480px;
            border-collapse: collapse;
            border: 2px solid #00796B;
        }

        .col-logo { width: 110px; }
        .col-info { width: 220px; }
        .col-qr   { width: 150px; }

        /* ===== KOLOM 1: LOGO ===== */
        .cell-logo {
            text-align: center;
            vertical-align: middle;
            padding: 12px 8px;
            border-right: 2px solid #00796B;
        }
        .cell-logo img {
            width: 72px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .logo-fallback {
            width: 72px;
            height: 50px;
            margin: 0 auto;
            border: 2px solid #00796B;
            border-bottom: none;
            display: inline-block;
        }

        /* ===== KOLOM 3: QR CODE ===== */
        .cell-qr {
            text-align: center;
            vertical-align: middle;
            padding: 12px 8px;
        }
        .scan-label {
            display: block;
            font-size: 7.5px;
            font-weight: 700;
            color: #00796B;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .qr-image {
            width: 78px;
            height: 78px;
            border: 1px solid #ddd;
            display: block;
            margin: 0 auto;
        }
        .qr-placeholder {
            width: 78px;
            height: 78px;
            margin: 0 auto;
            border: 1px solid #ddd;
            background: #f9f9f9;
            line-height: 78px;
            font-size: 11px;
            color: #999;
        }
        .qr-code-value {
            display: block;
            margin-top: 5px;
            font-family: 'Courier New', monospace;
            font-size: 8.5px;
            font-weight: 700;
            color: #00796B;
            letter-spacing: 0.3px;
            background: #E0F2F1;
            padding: 2px 4px;
            word-break: break-all;
        }

        /* ===== KOLOM 2: INFO (1 TABEL, 4 BARIS — TANPA NESTING TAMBAHAN) ===== */
        .cell-info-wrap {
            padding: 0;
            border-right: 2px solid #00796B;
            vertical-align: top;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #00796B;
            vertical-align: top;
        }
        .info-table tr:last-child td {
            border-bottom: none;
        }

        .title-text {
            font-size: 12px;
            font-weight: 700;
            color: #00796B;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            line-height: 1.2;
            display: block;
        }
        .subtitle-text {
            font-size: 7.5px;
            font-weight: 400;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            display: block;
        }

        .row-label {
            font-size: 7px;
            font-weight: 700;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            display: block;
            margin-bottom: 1px;
        }
        .row-value {
            font-size: 10px;
            font-weight: 700;
            color: #222;
            display: block;
        }

        /* Kode & Tanggal Beli: 2 sel LANGSUNG dalam info-table (tidak ada tabel bersarang lagi) */
        .td-kode {
            width: 58%;
        }
        .td-tanggal {
            width: 42%;
        }
        .row-value-code {
            font-size: 8px;
            word-break: break-all; /* boleh patah kata supaya TIDAK PERNAH mendorong lebar kolom */
        }
    </style>
</head>
<body>

    @php
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

        $qrText  = "==================================\n";
        $qrText .= " BARANG INVENTARIS\n";
        $qrText .= " SIT PERMATA MOJOKERTO\n";
        $qrText .= "==================================\n\n";
        $qrText .= "Nama Barang : " . $item->name . "\n";
        $qrText .= "Kode        : " . $item->code . "\n";
        $qrText .= "Unit        : " . ($item->unit->name ?? '-') . "\n";
        $qrText .= "Lokasi      : " . ($item->location ?? '-') . "\n";
        $qrText .= "Sumber Dana : " . ($item->fundingSource->name ?? '-') . "\n";
        $qrText .= "Tanggal Beli: " . ($item->purchase_date
            ? \Carbon\Carbon::parse($item->purchase_date)->translatedFormat('d/m/Y')
            : '-') . "\n";

        $qrDataUri = sticker_generate_qr_data_uri($qrText, 240);

        $logoPath = public_path('images/logopermata.png');
        $logoExists = file_exists($logoPath);

        $tanggalBeli = $item->purchase_date
            ? \Carbon\Carbon::parse($item->purchase_date)->translatedFormat('d/m/Y')
            : '-';
    @endphp

    <table class="sticker">
        <colgroup>
            <col class="col-logo">
            <col class="col-info">
            <col class="col-qr">
        </colgroup>
        <tr>
            {{-- ===== KOLOM 1: LOGO (center vertikal, TANPA rowspan) ===== --}}
            <td class="cell-logo">
                @if($logoExists)
                    <img src="{{ $logoPath }}" alt="Logo">
                @else
                    <span class="logo-fallback"></span>
                @endif
            </td>

            {{-- ===== KOLOM 2: INFO (1 tabel, 4 baris) ===== --}}
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
                            <span class="row-label">Kode</span>
                            <span class="row-value row-value-code">{{ $item->code }}</span>
                        </td>
                        <td class="td-tanggal">
                            <span class="row-label">Tanggal Beli</span>
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

            {{-- ===== KOLOM 3: QR CODE (center vertikal, TANPA rowspan) ===== --}}
            <td class="cell-qr">
                <span class="scan-label">Scan Me</span>
                @if($qrDataUri)
                    <img src="{{ $qrDataUri }}" alt="QR Code" class="qr-image" width="78" height="78">
                @else
                    <div class="qr-placeholder">QR</div>
                @endif
                <span class="qr-code-value">{{ $item->code }}</span>
            </td>
        </tr>
    </table>

</body>
</html>