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
        }

        /* ===== KARTU STIKER (TABLE BASED - AGAR AMAN DI DOMPDF) ===== */
        .sticker {
            width: 460px;
            margin: 0 auto;
            border: 2px solid #00796B;
            border-radius: 6px;
            background: white;
            border-collapse: collapse;
        }
        .sticker > tbody > tr > td {
            vertical-align: middle;
        }

        /* ===== KOLOM 1: LOGO ===== */
        .col-logo {
            width: 90px;
            padding: 8px 6px;
            border-right: 2px solid #00796B;
            text-align: center;
        }
        .col-logo img {
            width: 70px;
            height: auto;
        }
        .logo-fallback {
            font-size: 12px;
            font-weight: 700;
            color: #00796B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ===== KOLOM 2: INFO (TABLE DALAM TABLE, 4 BARIS) ===== */
        .col-info {
            padding: 0;
            border-right: 2px solid #00796B;
        }
        .info-table-inner {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table-inner td {
            padding: 6px 10px;
            border-bottom: 1px solid #00796B;
            vertical-align: middle;
        }
        .info-table-inner tr:last-child td {
            border-bottom: none;
        }
        .row-label {
            display: block;
            font-size: 7px;
            font-weight: 700;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 1px;
        }
        .row-value {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #222;
            line-height: 1.25;
        }

        /* baris 1: judul + sub judul */
        .title {
            font-size: 12px;
            font-weight: 700;
            color: #00796B;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            line-height: 1.2;
        }
        .subtitle {
            font-size: 8px;
            font-weight: 400;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* baris 2: kode & tanggal (dua sel sejajar) */
        .kode-tanggal-row td {
            width: 50%;
        }

        /* ===== KOLOM 3: QR CODE ===== */
        .col-qr {
            width: 110px;
            padding: 8px 6px;
            text-align: center;
        }
        .scan-label {
            display: block;
            font-size: 8px;
            font-weight: 700;
            color: #00796B;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .qr-image-wrap img {
            width: 78px;
            height: 78px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #fff;
        }
        .qr-placeholder {
            width: 78px;
            height: 78px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f9f9f9;
            display: inline-block;
            line-height: 78px;
            font-size: 11px;
            color: #999;
        }
        .qr-code-value {
            display: block;
            margin-top: 5px;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            font-weight: 700;
            color: #00796B;
            letter-spacing: 0.4px;
            background: #E0F2F1;
            padding: 2px 8px;
            border-radius: 3px;
        }
    </style>
</head>
<body>

    <table class="sticker">
        <tbody>
        <tr>

            <!-- ===== KOLOM 1: LOGO ===== -->
            <td class="col-logo">
                @if(file_exists(public_path('images/logopermata.png')))
                    <img src="{{ public_path('images/logopermata.png') }}" alt="Logo SIT Permata">
                @else
                    <span class="logo-fallback">LOGO</span>
                @endif
            </td>

            <!-- ===== KOLOM 2: INFO (4 BARIS) ===== -->
            <td class="col-info">
                <table class="info-table-inner">
                    <tbody>
                    <!-- baris 1: judul & sub judul -->
                    <tr>
                        <td colspan="2">
                            <span class="title">Barang Inventaris</span>
                            <span class="subtitle">Milik SIT Permata Mojokerto</span>
                        </td>
                    </tr>

                    <!-- baris 2: kode barang & tanggal beli (sejajar) -->
                    <tr class="kode-tanggal-row">
                        <td>
                            <span class="row-label">Kode</span>
                            <span class="row-value">{{ $item->code }}</span>
                        </td>
                        <td>
                            <span class="row-label">Tanggal</span>
                            <span class="row-value">{{ $item->purchase_date ? \Carbon\Carbon::parse($item->purchase_date)->translatedFormat('d/m/Y') : '-' }}</span>
                        </td>
                    </tr>

                    <!-- baris 3: nama barang -->
                    <tr>
                        <td colspan="2">
                            <span class="row-label">Nama Barang</span>
                            <span class="row-value">{{ $item->name }}</span>
                        </td>
                    </tr>

                    <!-- baris 4: sumber dana -->
                    <tr>
                        <td colspan="2">
                            <span class="row-label">Sumber Dana</span>
                            <span class="row-value">{{ $item->fundingSource->name ?? '-' }}</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>

            <!-- ===== KOLOM 3: QR CODE ===== -->
            <td class="col-qr">
                <span class="scan-label">Scan Me</span>
                <div class="qr-image-wrap">
                    @if($item->qr_code_path && file_exists(public_path('storage/' . $item->qr_code_path)))
                        <img src="{{ public_path('storage/' . $item->qr_code_path) }}" alt="QR Code">
                    @else
                        <div class="qr-placeholder">QR</div>
                    @endif
                </div>
                <span class="qr-code-value">{{ $item->code }}</span>
            </td>

        </tr>
        </tbody>
    </table>

</body>
</html>