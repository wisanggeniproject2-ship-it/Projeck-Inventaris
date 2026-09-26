<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Berita Acara Penghapusan Aset</title>
    <style>
        /* ============================================================ */
        /*  PAGE SETUP — margin 0 karena background full A4            */
        /* ============================================================ */
        @page {
            size: A4 portrait;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            width: 210mm;
            height: 297mm;
            overflow: hidden;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            color: #1a1a1a;
            line-height: 1.4;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ============================================================ */
        /*  BACKGROUND TEMPLATE — full A4                               */
        /* ============================================================ */
        .bg-template {
            position: absolute;
            top: 0;
            left: 0;
            width: 210mm;
            height: 297mm;
            z-index: -1;
        }

        .bg-template img {
            width: 210mm;
            height: 297mm;
            display: block;
        }

        /* ============================================================ */
        /*  KONTEN                                                      */
        /* ============================================================ */
        .konten {
            position: relative;
            padding: 62mm 25mm 25mm 25mm;
            z-index: 1;
        }

        /* ============================================================ */
        /*  TANGGAL SURAT                                               */
        /* ============================================================ */
        .tgl-surat {
            text-align: right;
            margin-bottom: 8px;
            font-size: 10pt;
        }

        /* ============================================================ */
        /*  NOMOR / LAMP / HAL                                          */
        /* ============================================================ */
        .nomor-hal {
            margin-bottom: 12px;
            font-size: 10pt;
            line-height: 1.5;
        }

        .nomor-hal table {
            border-collapse: collapse;
        }

        .nomor-hal table td {
            vertical-align: top;
            padding: 0;
            font-size: 10pt;
        }

        .nomor-hal table td.label {
            width: 60px;
        }

        .nomor-hal table td.sep {
            width: 12px;
            text-align: center;
        }

        /* ============================================================ */
        /*  JUDUL TENGAH                                                */
        /* ============================================================ */
        .judul-tengah {
            text-align: center;
            margin: 12px 0 10px 0;
        }

        .judul-tengah h2 {
            font-size: 12pt;
            font-weight: bold;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            text-decoration: underline;
            line-height: 1.3;
        }

        /* ============================================================ */
        /*  SALAM PEMBUKA                                               */
        /* ============================================================ */
        .salam {
            margin-bottom: 6px;
            font-size: 10pt;
        }

        /* ============================================================ */
        /*  PARAGRAF ISI                                                */
        /* ============================================================ */
        .paragraf {
            text-align: justify;
            margin-bottom: 5px;
            line-height: 1.5;
            font-size: 10pt;
        }

        /* ============================================================ */
        /*  TABEL DATA (indent list style)                              */
        /* ============================================================ */
        .data-aset {
            margin: 5px 0 8px 30px;
            font-size: 10pt;
        }

        .data-aset table {
            border-collapse: collapse;
        }

        .data-aset table td {
            vertical-align: top;
            padding: 1px 0;
            font-size: 10pt;
            line-height: 1.4;
        }

        .data-aset table td.label {
            width: 140px;
        }

        .data-aset table td.sep {
            width: 12px;
            text-align: center;
        }

        .data-aset table td.value {
            font-weight: bold;
        }

        .data-aset .highlight {
            display: inline-block;
            background: #eef1f4;
            border: 1px solid #ccd3da;
            padding: 1px 5px;
            border-radius: 2px;
            font-family: 'Courier New', monospace;
            font-size: 8pt;
            font-weight: normal;
            letter-spacing: 0.2px;
            word-break: break-all;
        }

        .data-aset .highlight-teal {
            display: inline-block;
            background: #e6f4f1;
            border: 1px solid #a8d5cc;
            padding: 1px 5px;
            border-radius: 2px;
            font-family: 'Courier New', monospace;
            font-size: 8pt;
            font-weight: normal;
            letter-spacing: 0.2px;
            word-break: break-all;
            color: #0F6B5F;
        }

        /* ============================================================ */
        /*  PENUTUP                                                     */
        /* ============================================================ */
        .penutup {
            margin-top: 8px;
            margin-bottom: 5px;
            font-size: 10pt;
            text-align: justify;
        }

        /* ============================================================ */
        /*  SALAM PENUTUP                                               */
        /* ============================================================ */
        .salam-penutup {
            margin-top: 8px;
            font-size: 10pt;
        }

        /* ============================================================ */
        /*  TANDA TANGAN — TANPA NAMA (cuma garis)                     */
        /* ============================================================ */
        .ttd-wrapper {
            margin-top: 14px;
            text-align: right;
            page-break-inside: avoid;
        }

        .ttd-box {
            display: inline-block;
            text-align: center;
            min-width: 220px;
            margin-left: auto;
        }

        .ttd-box .jabatan {
            font-size: 10pt;
            margin-bottom: 0;
            line-height: 1.4;
        }

        /* 🔥 GARIS TTD — tanpa nama di bawahnya */
        .ttd-line {
            display: block;
            width: 70%;
            border-top: 1px solid #000;
            margin: 55px auto 5px auto;
        }

        .ttd-box .jabatan-bawah {
            font-size: 9pt;
            color: #333;
            margin-top: 2px;
        }
    </style>
</head>
<body>

    {{-- ============================================================ --}}
    {{-- BACKGROUND TEMPLATE                                          --}}
    {{-- ============================================================ --}}
    <div class="bg-template">
        @php
            $templatePath = public_path('images/pdfaset.jpeg');
            $templateDataUri = null;

            if (file_exists($templatePath)) {
                $templateDataUri = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($templatePath));
            }
        @endphp

        @if($templateDataUri)
            <img src="{{ $templateDataUri }}" alt="Template Berita Acara">
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- KONTEN                                                       --}}
    {{-- ============================================================ --}}
    <div class="konten">

        {{-- TANGGAL SURAT --}}
        <div class="tgl-surat">
            Mojokerto, {{ $disposal->approved_at ? $disposal->approved_at->locale('id')->translatedFormat('d F Y') : now()->locale('id')->translatedFormat('d F Y') }}
        </div>

        {{-- NOMOR / LAMP / HAL --}}
        <div class="nomor-hal">
            <table>
                <tr>
                    <td class="label">Nomor</td>
                    <td class="sep">:</td>
                    <td>BA/{{ str_pad($disposal->id, 3, '0', STR_PAD_LEFT) }}/BA-KU/YPM/{{ $disposal->approved_at ? $disposal->approved_at->format('m') : date('m') }}/{{ $disposal->approved_at ? $disposal->approved_at->format('Y') : date('Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Lamp</td>
                    <td class="sep">:</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td class="label">Hal</td>
                    <td class="sep">:</td>
                    <td><strong>Berita Acara Penghapusan Aset</strong></td>
                </tr>
            </table>
        </div>

        {{-- JUDUL TENGAH --}}
        <div class="judul-tengah">
            <h2>Berita Acara Penghapusan Aset</h2>
        </div>

        {{-- SALAM PEMBUKA --}}
        <div class="salam">
            <em>Assalaamu'alaikum warahmatullahi wabarakatuh</em>
        </div>

        {{-- PEMBUKAAN --}}
        <div class="paragraf">
            Pada hari ini, <strong>{{ $disposal->approved_at ? $disposal->approved_at->locale('id')->translatedFormat('l') : '-' }}</strong>,
            tanggal <strong>{{ $disposal->approved_at ? $disposal->approved_at->locale('id')->translatedFormat('d F Y') : '-' }}</strong>,
            bertempat di <strong>{{ $disposal->item->unit->name ?? '-' }}</strong>, kami yang bertanda tangan di bawah ini
            telah melakukan pemeriksaan/penelitian atas aset berupa:
        </div>

        {{-- TABEL DATA ASET --}}
        <div class="data-aset">
            <table>
                <tr>
                    <td class="label">Nama Barang</td>
                    <td class="sep">:</td>
                    <td class="value">{{ $disposal->item->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Kode Barang</td>
                    <td class="sep">:</td>
                    <td class="value">
                        <span class="highlight">{{ $disposal->item->full_code ?? $disposal->item->code ?? '-' }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="label">Kode Stok</td>
                    <td class="sep">:</td>
                    <td class="value">
                        <span class="highlight-teal">{{ $disposal->stock_code ?? '-' }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="label">Jumlah Dihapus</td>
                    <td class="sep">:</td>
                    <td class="value">{{ $disposal->quantity ?? 1 }} unit</td>
                </tr>
                <tr>
                    <td class="label">Kategori</td>
                    <td class="sep">:</td>
                    <td class="value">{{ $disposal->item->category->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Unit</td>
                    <td class="sep">:</td>
                    <td class="value">{{ $disposal->item->unit->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Lokasi</td>
                    <td class="sep">:</td>
                    <td class="value">{{ $disposal->item->location ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Sumber Dana</td>
                    <td class="sep">:</td>
                    <td class="value">{{ $disposal->item->fundingSource->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal Perolehan</td>
                    <td class="sep">:</td>
                    <td class="value">
                        {{ $disposal->item->purchase_date ? \Carbon\Carbon::parse($disposal->item->purchase_date)->locale('id')->translatedFormat('d F Y') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Alasan Penghapusan</td>
                    <td class="sep">:</td>
                    <td class="value">{{ $disposal->reason ?? '-' }}</td>
                </tr>
            </table>
        </div>

        {{-- ISI BERITA ACARA --}}
        <div class="paragraf">
            Adapun hasil pemeriksaan/penelitian atas aset tersebut memiliki kondisi yang sudah
            <strong>barang tidak layak pakai</strong> dan tidak dapat dipergunakan untuk kepentingan pengelolaan
            inventaris, sehingga manfaat penggunaannya untuk kepentingan pelaksanaan tugas pokok
            dan fungsi tidak sebanding dengan biaya perbaikan yang akan dikeluarkan.
        </div>

        <div class="paragraf">
            Oleh karena itu, aset tersebut diusulkan untuk <strong>dihapus</strong> dari Buku
            Inventaris Aset Tetap dan/atau Buku Inventaris Aset Lainnya.
        </div>

        {{-- PENUTUP --}}
        <div class="penutup">
            Demikian Berita Acara ini kami buat dengan sebenarnya dan untuk dipergunakan
            sebagaimana mestinya.
        </div>

        {{-- SALAM PENUTUP --}}
        <div class="salam-penutup">
            <em>Wassalaamu'alaikum warahmatullahi wabarakatuh</em>
        </div>

        {{-- 🔥 TANDA TANGAN — TANPA NAMA (cuma garis + jabatan) --}}
        <div class="ttd-wrapper">
            <div class="ttd-box">
                <div class="jabatan">
                    Mojokerto, {{ now()->locale('id')->translatedFormat('d F Y') }}<br>
                    Penanggung Jawab,
                </div>
                <div class="ttd-line"></div>
                <div class="jabatan-bawah">Petugas Inventaris</div>
            </div>
        </div>

    </div>

</body>
</html>