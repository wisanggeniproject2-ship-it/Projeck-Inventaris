<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Berita Acara Penghapusan Aset</title>
    <style>
        /* ============================================================ */
        /*  PAGE SETUP — MARGIN ASIMETRIS (kanan lebih lega)            */
        /* ============================================================ */
        @page {
            size: A4 portrait;
            margin: 20mm 30mm 20mm 25mm;   /* 🔥 kanan 30mm biar gak mepet */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10.5pt;
            color: #1a1a1a;
            line-height: 1.5;
        }

        /* ============================================================ */
        /*  KONTEN UTAMA — PADDING EXTRA KIRI-KANAN                     */
        /* ============================================================ */
        .konten-utama {
            padding: 2mm 5mm 20mm 5mm;   /* 🔥 kiri-kanan 5mm, bottom 20mm */
        }

        /* ============================================================ */
        /*  KOP SURAT / HEADER                                          */
        /* ============================================================ */
        .header {
            text-align: center;
            border-bottom: 2.5px double #1a1a1a;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }

        .header .yayasan {
            font-size: 15pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            line-height: 1.25;
        }

        .header .sub-yayasan {
            font-size: 9.5pt;
            font-weight: bold;
            letter-spacing: 0.3px;
            margin-top: 3px;
            color: #333;
        }

        .header .alamat {
            font-size: 8.5pt;
            color: #555;
            margin-top: 4px;
            line-height: 1.35;
        }

        /* ============================================================ */
        /*  JUDUL BERITA ACARA                                          */
        /* ============================================================ */
        .judul {
            text-align: center;
            margin-bottom: 18px;
        }

        .judul h2 {
            font-size: 13pt;
            font-weight: bold;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 5px;
            line-height: 1.3;
        }

        .judul .nomor {
            display: inline-block;
            font-size: 9pt;
            color: #333;
            padding: 2px 10px;
            border: 1px solid #999;
            border-radius: 2px;
            background: #fafafa;
        }

        /* ============================================================ */
        /*  PARAGRAF ISI                                                */
        /* ============================================================ */
        .paragraf {
            text-align: justify;
            text-indent: 35px;
            margin-bottom: 10px;
            line-height: 1.6;
        }

        /* ============================================================ */
        /*  SUB HEADING                                                 */
        /* ============================================================ */
        .sub-heading {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin: 12px 0 6px 0;
            padding-bottom: 3px;
            border-bottom: 1px solid #bbb;
        }

        /* ============================================================ */
        /*  TABEL DATA BARANG                                           */
        /* ============================================================ */
        .data-barang-wrapper {
            margin: 6px 0 14px 0;
            border: 1px solid #999;
            border-radius: 3px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .data-barang {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;   /* 🔥 kunci lebar kolom */
        }

        .data-barang tr:nth-child(even) {
            background: #f6f6f6;
        }

        .data-barang tr + tr td {
            border-top: 1px solid #e0e0e0;
        }

        .data-barang td {
            padding: 5px 10px;
            vertical-align: top;
            font-size: 10pt;
            line-height: 1.4;
        }

        .data-barang td.label {
            width: 160px;
            color: #333;
            font-weight: bold;
            word-wrap: break-word;
        }

        .data-barang td.sep {
            width: 14px;
            text-align: center;
            color: #888;
        }

        .data-barang td.value {
            font-weight: bold;
            word-wrap: break-word;
            word-break: break-word;
            color: #000;
        }

        .data-barang .highlight {
            display: inline-block;
            background: #eef1f4;
            border: 1px solid #ccd3da;
            padding: 1px 6px;
            border-radius: 2px;
            font-family: 'Courier New', monospace;
            font-size: 8.5pt;
            font-weight: normal;
            letter-spacing: 0.3px;
            word-break: break-all;
        }

        /* ============================================================ */
        /*  TANDA TANGAN                                                */
        /* ============================================================ */
        .ttd-section {
            margin-top: 15px;
            page-break-inside: avoid;
        }

        .ttd-row {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .ttd-row td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 14px;
        }

        .ttd-title {
            font-size: 10pt;
            margin-bottom: 55px;
            line-height: 1.4;
        }

        /* 🔥 Garis TTD — tanpa nama di bawahnya */
        .ttd-line {
            display: inline-block;
            width: 70%;
            border-top: 1px solid #000;
            margin-top: 55px;
        }

        /* ============================================================ */
        /*  FOOTER — NEMPEL BAWAH (dengan bottom offset)                */
        /* ============================================================ */
        .footer-cetak {
            position: fixed;
            bottom: 10mm;              /* 🔥 dari 0 → 10mm biar gak ilang */
            left: 0;
            right: 0;
            padding-top: 6px;
            border-top: 1px dashed #aaa;
            font-size: 7.5pt;
            color: #666;
            text-align: center;
            font-style: italic;
        }
    </style>
</head>
<body>

    <div class="konten-utama">

        {{-- KOP SURAT --}}
        <div class="header">
            <div class="yayasan">Yayasan Permata</div>
            <div class="sub-yayasan">Sistem Manajemen Inventaris</div>
            <div class="alamat">
                Mojokerto, Jawa Timur<br>
                Indonesia
            </div>
        </div>

        {{-- JUDUL --}}
        <div class="judul">
            <h2>Berita Acara Penghapusan Aset</h2>
            <div class="nomor">
                Nomor: BA/{{ str_pad($disposal->id, 3, '0', STR_PAD_LEFT) }}/{{ $disposal->approved_at ? $disposal->approved_at->format('m') : date('m') }}/{{ $disposal->approved_at ? $disposal->approved_at->format('Y') : date('Y') }}
            </div>
        </div>

        {{-- PEMBUKAAN --}}
        <div class="paragraf">
            Pada hari ini, <strong>{{ $disposal->approved_at ? $disposal->approved_at->locale('id')->translatedFormat('l') : '-' }}</strong>,
            tanggal <strong>{{ $disposal->approved_at ? $disposal->approved_at->locale('id')->translatedFormat('d F Y') : '-' }}</strong>,
            bertempat di <strong>{{ $disposal->item->unit->name ?? '-' }}</strong>,
            kami yang bertanda tangan di bawah ini telah melakukan pemeriksaan/penelitian atas aset berupa:
        </div>

        {{-- SUB HEADING --}}
        <div class="sub-heading">Data Aset yang Dihapus</div>

        {{-- TABEL DATA BARANG --}}
        <div class="data-barang-wrapper">
            <table class="data-barang">
                <colgroup>
                    <col style="width: 160px;">
                    <col style="width: 14px;">
                    <col>
                </colgroup>
                <tr>
                    <td class="label">Nama Barang</td>
                    <td class="sep">:</td>
                    <td class="value">{{ $disposal->item->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Kode Stok</td>
                    <td class="sep">:</td>
                    <td class="value">
                        <span class="highlight">{{ $disposal->stock_code ?? '-' }}</span>
                    </td>
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

        <div class="paragraf">
            Demikian Berita Acara ini kami buat dengan sebenarnya dan untuk dipergunakan
            sebagaimana mestinya.
        </div>

        {{-- TANDA TANGAN — TANPA NAMA --}}
        <div class="ttd-section">
            <table class="ttd-row">
                <tr>
                    <td>
                        <div class="ttd-title">Yang Mengajukan,</div>
                        <div class="ttd-line"></div>
                    </td>
                    <td>
                        <div class="ttd-title">Penanggung Jawab,</div>
                        <div class="ttd-line"></div>
                    </td>
                </tr>
            </table>
        </div>

    </div>

    {{-- FOOTER --}}
    <div class="footer-cetak">
        Dicetak pada: <strong>{{ now()->locale('id')->translatedFormat('d F Y') }}</strong>
        &nbsp;pukul&nbsp;<strong>{{ now()->format('H:i') }} WIB</strong>
        &nbsp;|&nbsp;
        Sistem Inventaris Yayasan Permata
    </div>

</body>
</html>