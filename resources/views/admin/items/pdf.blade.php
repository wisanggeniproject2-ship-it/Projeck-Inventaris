<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stiker Barang</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            background: white;
            padding: 10px;
        }

        /* ===== KARTU STIKER =====
           Tidak memakai display:flex — banyak PDF engine
           dukungan flexbox-nya terbatas/diabaikan. */
        .sticker {
            width: 280px;
            margin: 0 auto;
            border: 2px solid #00796B;
            border-radius: 6px;
            padding: 16px 14px;
            background: white;
            text-align: center;
        }

        /* ===== LOGO ===== */
        .logo {
            font-size: 10px;
            font-weight: bold;
            color: #00796B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.3;
            margin-bottom: 4px;
        }
        .logo .icon {
            display: block;
            width: 24px;
            height: 20px;
            margin: 0 auto 4px auto;
            border: 2px solid #00796B;
            border-bottom: none;
            border-radius: 3px 3px 0 0;
            position: relative;
        }
        .logo .icon::after {
            content: "";
            position: absolute;
            left: -4px;
            right: -4px;
            bottom: -3px;
            height: 3px;
            background: #00796B;
            border-radius: 1px;
        }
        .logo .sub {
            font-size: 7px;
            color: #666;
            font-weight: normal;
            letter-spacing: 0.5px;
        }

        /* ===== JUDUL ===== */
        .header {
            border-top: 2px solid #00796B;
            border-bottom: 2px solid #00796B;
            padding: 4px 0;
            margin: 4px 0 10px 0;
        }
        .header h1 {
            font-size: 11px;
            font-weight: bold;
            color: #00796B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header p {
            font-size: 7px;
            color: #666;
            letter-spacing: 0.3px;
        }

        /* ===== BLOK QR ===== */
        .qr-block {
            width: 100%;
            margin-bottom: 8px;
        }
        .scan-label {
            display: block;
            font-size: 7px;
            font-weight: bold;
            color: #00796B;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .qr-image-row {
            display: block;
            width: 100%;
            text-align: center;
        }
        .qr-image-row img {
            width: 80px;
            height: 80px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .qr-placeholder {
            width: 80px;
            height: 80px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
            line-height: 80px;
            background: #f9f9f9;
            margin: 0 auto;
            font-size: 10px;
            color: #999;
        }
        .qr-code-row {
            display: block;
            width: 100%;
            text-align: center;
            margin-top: 6px;
        }
        .qr-code-value {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            font-weight: bold;
            color: #00796B;
            letter-spacing: 0.5px;
            background: #E0F2F1;
            padding: 3px 14px;
            border-radius: 3px;
        }

        /* ===== INFO BARANG =====
           Diganti dari float ke TABLE. Table adalah elemen paling
           dasar & paling stabil di semua PDF engine (DomPDF, mPDF,
           wkhtmltopdf) — tidak butuh clearfix, tidak bisa "menumpuk"
           seperti float, dan tidak bisa "sejajar keliru" seperti flex. */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        .info-table td {
            padding: 5px 0;
            font-size: 9px;
            border-bottom: 1px dashed #eee;
            vertical-align: middle;
        }
        .info-table tr.no-border td {
            border-bottom: none;
        }
        .info-table td.label {
            text-align: left;
            font-weight: 600;
            color: #333;
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            width: 40%;
        }
        .info-table td.value {
            text-align: right;
            color: #00796B;
            font-weight: 600;
            font-size: 9px;
            width: 60%;
        }

        @media (max-width: 350px) {
            .sticker {
                width: 100%;
                padding: 12px 10px;
            }
            .qr-image-row img,
            .qr-placeholder {
                width: 60px;
                height: 60px;
                line-height: 60px;
            }
            .qr-code-value {
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="sticker">

        <!-- ===== LOGO ===== -->
        <div class="logo">
            <span class="icon"></span>
            SIT PERMATA
            <span class="sub">MOJOKERTO</span>
        </div>

        <!-- ===== JUDUL ===== -->
        <div class="header">
            <h1>BARANG INVENTARIS</h1>
            <p>MILIK SIT PERMATA MOJOKERTO</p>
        </div>

        <!-- ===== BLOK QR: SCAN ME -> GAMBAR QR -> KODE ANGKA ===== -->
        <div class="qr-block">

            <div class="scan-label">Scan Me</div>

            <div class="qr-image-row">
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

        <!-- ===== INFO BARANG (pakai table, bukan float) ===== -->
        <table class="info-table">
            <tr>
                <td class="label">NAMA</td>
                <td class="value">{{ $item->name }}</td>
            </tr>
            <tr>
                <td class="label">UNIT</td>
                <td class="value">{{ $item->unit->name ?? '-' }}</td>
            </tr>
            <tr class="no-border">
                <td class="label">TANGGAL BELI</td>
                <td class="value">{{ $item->purchase_date ? \Carbon\Carbon::parse($item->purchase_date)->translatedFormat('d/m/Y') : '-' }}</td>
            </tr>
        </table>

    </div>
</body>
</html>