<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stiker Barang</title>
    <style>
        /* reset & dasar */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: #f2f2f2;
            font-family: 'Arial', 'Helvetica', sans-serif;
            padding: 20px 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* ===== KARTU STIKER ===== */
        .sticker {
            width: 300px;
            margin: 0 auto;
            border: 2px solid #00796B;
            border-radius: 6px;
            padding: 14px 12px 12px 12px;
            background: white;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        /* ===== LOGO ATAS ===== */
        .logo-area {
            margin-bottom: 4px;
        }
        .logo-icon {
            display: block;
            width: 26px;
            height: 18px;
            margin: 0 auto 4px auto;
            border: 2px solid #00796B;
            border-bottom: none;
            border-radius: 3px 3px 0 0;
            position: relative;
        }
        .logo-icon::after {
            content: "";
            position: absolute;
            left: -4px;
            right: -4px;
            bottom: -3px;
            height: 3px;
            background: #00796B;
            border-radius: 1px;
        }
        .logo-title {
            font-size: 12px;
            font-weight: 700;
            color: #00796B;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1.2;
        }
        .logo-sub {
            font-size: 8px;
            font-weight: 400;
            color: #666;
            letter-spacing: 0.5px;
            display: block;
        }

        /* ===== HEADER / JUDUL ===== */
        .header-block {
            border-top: 2px solid #00796B;
            border-bottom: 2px solid #00796B;
            padding: 5px 0 4px 0;
            margin: 6px 0 10px 0;
        }
        .header-block h1 {
            font-size: 13px;
            font-weight: 700;
            color: #00796B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }
        .header-block .subhead {
            font-size: 8px;
            font-weight: 400;
            color: #666;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        /* ===== BLOK QR ===== */
        .qr-section {
            margin-bottom: 8px;
        }
        .scan-label {
            display: block;
            font-size: 9px;
            font-weight: 700;
            color: #00796B;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-bottom: 4px;
        }
        .qr-image-wrap {
            display: block;
            text-align: center;
        }
        .qr-image-wrap img {
            width: 80px;
            height: 80px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #fff;
        }
        .qr-placeholder {
            width: 80px;
            height: 80px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f9f9f9;
            display: inline-block;
            line-height: 80px;
            font-size: 12px;
            color: #999;
            text-align: center;
        }
        .qr-code-row {
            margin-top: 6px;
            text-align: center;
        }
        .qr-code-value {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            font-weight: 700;
            color: #00796B;
            letter-spacing: 0.5px;
            background: #E0F2F1;
            padding: 2px 16px;
            border-radius: 3px;
            display: inline-block;
        }

        /* ===== INFO TABLE ===== */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .info-table td {
            padding: 6px 0 5px 0;
            font-size: 10px;
            border-bottom: 1px dashed #e0e0e0;
            vertical-align: middle;
        }
        .info-table tr.no-border td {
            border-bottom: none;
        }
        .info-table .label-col {
            text-align: left;
            font-weight: 700;
            color: #222;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            width: 38%;
        }
        .info-table .value-col {
            text-align: right;
            color: #00796B;
            font-weight: 700;
            font-size: 10px;
            width: 62%;
        }

        /* ===== RESPONSIF ===== */
        @media (max-width: 360px) {
            .sticker {
                width: 100%;
                padding: 12px 10px;
            }
            .qr-image-wrap img,
            .qr-placeholder {
                width: 66px;
                height: 66px;
                line-height: 66px;
            }
            .qr-code-value {
                font-size: 11px;
                padding: 2px 12px;
            }
        }
    </style>
</head>
<body>
    <div class="sticker">

        <!-- ===== LOGO ===== -->
        <div class="logo-area">
            <span class="logo-icon"></span>
            <span class="logo-title">SIT PERMATA</span>
            <span class="logo-sub">MOJOKERTO</span>
        </div>

        <!-- ===== HEADER JUDUL ===== -->
        <div class="header-block">
            <h1>BARANG INVENTARIS</h1>
            <span class="subhead">MILIK SIT PERMATA MOJOKERTO</span>
        </div>

        <!-- ===== QR + KODE ===== -->
        <div class="qr-section">
            <span class="scan-label">Scan Me</span>

            <div class="qr-image-wrap">
                @if($item->qr_code_path && file_exists(public_path('storage/' . $item->qr_code_path)))
                    <img src="{{ public_path('storage/' . $item->qr_code_path) }}" alt="QR Code">
                @else
                    <div class="qr-placeholder">QR</div>
                @endif
            </div>

            <div class="qr-code-row">
                <span class="qr-code-value">{{ $item->code }}</span>
            </div>
        </div>

        <!-- ===== INFO BARANG (TABLE) ===== -->
        <table class="info-table">
            <tr>
                <td class="label-col">NAMA</td>
                <td class="value-col">{{ $item->name }}</td>
            </tr>
            <tr>
                <td class="label-col">UNIT</td>
                <td class="value-col">{{ $item->unit->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">SUMBER DANA</td>
                <td class="value-col">{{ $item->fundingSource->name ?? '-' }}</td>
            </tr>
            <tr class="no-border">
                <td class="label-col">TANGGAL BELI</td>
                <td class="value-col">{{ $item->purchase_date ? \Carbon\Carbon::parse($item->purchase_date)->translatedFormat('d/m/Y') : '-' }}</td>
            </tr>
        </table>

    </div>
</body>
</html>