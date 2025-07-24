{{-- resources/views/vendor_pos/penawaran_pdf.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Penawaran {{ $penawaran->nomor_penawaran }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@400;600;700&display=swap" rel="stylesheet" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Mulish', sans-serif;
            color: #2f4f2f;
            background-color: #f0f5f0;
            line-height: 1.5;
        }
        .container {
            width: 720px;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            padding: 30px 40px;
        }
        h1, h2, h3, h4 {
            color: #34623f;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header-left img,
        .header-right img {
            max-height: 60px;
        }
        .header-mid {
            text-align: center;
            flex: 1;
        }
        .header-mid h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 4px;
            letter-spacing: 1px;
        }
        .header-mid h2 {
            font-size: 14px;
            font-weight: 600;
            color: #6b8e6b;
        }
        .header-info {
            text-align: right;
            font-size: 12px;
            color: #555;
            margin-top: 6px;
        }
        .party-info {
            margin: 25px 0;
            font-size: 13px;
            color: #444;
        }
        .party-info strong {
            color: #2f4f2f;
        }
        .intro-text {
            font-size: 13px;
            color: #4a4a4a;
            margin-bottom: 25px;
            text-align: justify;
        }
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 13px;
        }
        .item-table th,
        .item-table td {
            border: 1px solid #c4d0c4;
            padding: 10px 12px;
        }
        .item-table th {
            background-color: #6b8e6b;
            color: #fff;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
        }
        .item-table tr:nth-child(even) td {
            background-color: #f7f9f7;
        }
        .item-table td {
            background-color: #fff;
            color: #333;
        }
        .item-table td:first-child,
        .item-table th:first-child {
            text-align: left;
        }
        .item-table td:nth-child(2),
        .item-table td:nth-child(3),
        .item-table td:nth-child(4) {
            text-align: right;
        }
        .totals {
            width: 100%;
            margin-bottom: 30px;
            font-size: 13px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }
        .totals-row span {
            color: #333;
            font-weight: 500;
        }
        .totals-row strong {
            color: #2f4f2f;
            font-weight: 600;
        }
        .totals-container {
            background-color: #e1eace;
            padding: 12px 15px;
            border-radius: 6px;
        }
        .closing {
            font-size: 13px;
            color: #444;
            margin-top: 30px;
            text-align: justify;
        }
        .closing strong {
            color: #2f4f2f;
        }
        .footer {
            margin-top: 45px;
            font-size: 11px;
            color: #666;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <div class="header-left">
                <img src="{{ public_path('images/logo_crushed_stone.png') }}" alt="Logo Crushed Stone" />
            </div>
            <div class="header-mid">
                <h1><u>Quotation<</u></h1>
                <h2>No. {{ $penawaran->nomor_penawaran }}</h2>
                <div class="header-info">
                    Jakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Hal : Penawaran Harga Batu Crushed Stone
                </div>
            </div>
           
        </div>

        {{-- Info Penerima --}}
        <div class="party-info">
            Kepada Yth:<br>
            <strong>PT. {{ $penawaran->customer->nama_perusahaan }}</strong><br>
            {{ $penawaran->customer->alamat_perusahaan ?? 'Di Tempat' }}<br>
            
            <span style="display: inline-block; margin-top: 4px;"><u>Bp. Iwan</u> <br> Purchasing</span>

            <br>
            
        </div>

        <div class="party-info">
        <span style="display: inline-block; margin-top: 0px;">Dear Sir,</span>
        </div>
        {{-- Intro --}}
        <div class="intro-text">
            Bersama surat ini, perkenankan kami untuk memperkenalkan, bahwa kami dari 
            <strong>PT. Tri Daya Selaras</strong> sebagai badan usaha berbadan hukum dan memiliki izin transportasi,
            bergerak di bidang transportasi tambang. Dengan pengalaman, jaminan produk, sumber daya,
            serta sarana, kami percaya mampu untuk memenuhi kebutuhan untuk <strong>PT. {{ $penawaran->customer->nama_perusahaan }}</strong>.
            Oleh karena itu, kami ingin menawarkan kepada perusahaan Bapak/Ibu rincian sebagai berikut:
        </div>

        {{-- Tabel Item --}}
        <table class="item-table">
            <thead>
                <tr>
                    <th style="width:5%;">No.</th>
                    <th style="width:50%;">Produk</th>
                    <th style="width:13%;">Volume</th>
                    <th style="width:13%;">Harga</th>
                    <th style="width:19%;">Jumlah Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penawaran->items as $idx => $item)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td style="text-align:left;">{{ $item->produk->nama_produk }}</td>
                        <td>{{ number_format($item->volume_order, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->harga_tebus, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->jumlah_harga, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right; font-weight: 600;">Subtotal</td>
                    <td>Rp {{ number_format($penawaran->subtotal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: right; font-weight: 600;">PPN 11%</td>
                    <td>Rp {{ number_format($penawaran->ppn11, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: right; font-weight: 700; background: #f0f5f0;">Total Harga</td>
                    <td style="font-weight: 700; background: #f0f5f0;">Rp {{ number_format($penawaran->total, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>


        {{-- Syarat & Ketentuan --}}
        <div style="border: 1px solid #c4d0c4; border-radius: 4px; padding: 8px 10px; margin-top: 15px; background-color: #f9fdf9; font-size: 11px;">
            <strong style="color: #34623f; display: block; margin-bottom: 6px;">Syarat & Ketentuan</strong>
            <ol style="padding-left: 15px; margin: 0;">
                <li>Metode Pembayaran: CREDIT 30 Hari Setelah Invoice diterima</li>
                <li>Metode Pemesanan: PO maksimal H-2 sebelum pengiriman</li>
                <li>Metode Pengiriman: Setelah konfirmasi PO</li>
                <li>Masa Berlaku Harga: 01 Juni 2025 s/d 14 Juni 2025</li>
                <li>Toleransi: 0.5% dari total jumlah pengiriman</li>
            </ol>
        </div>

        {{-- Penutup --}}
        <div class="closing">
            Demikian surat penawaran ini kami sampaikan. Kami sangat berharap dapat diberikan kesempatan dan 
            kepercayaan kepada kami untuk dapat berbisnis dengan perusahaan Bapak/Ibu. 
            Atas perhatian, pertimbangan, dan kerja sama yang baik, kami ucapkan terima kasih.
        </div>

        <div style="display: table; width: 100%; margin-top: 30px; font-size: 11px;">
            {{-- Kolom TTD --}}
            <div style="display: table-cell; width: 50%; vertical-align: top;">
                <p>Hormat kami,</p>
                <p><strong>PT. Tri Daya Selaras</strong></p>
                <br><br><br><br> {{-- Spasi untuk tanda tangan --}}
                <p><strong><u>Branch Manager</u></strong></p>
            </div>
        
            {{-- Kolom Kontak Person --}}
            <div style="display: table-cell; width: 50%; vertical-align: top;">
                <div style="border: 1px solid #c4d0c4; border-radius: 4px; padding: 10px; background-color: #f9fdf9;">
                    <p style="margin: 0 0 6px; color: #34623f;"><strong>Kontak Person</strong></p>
                    <p style="margin: 2px 0;"><strong>Nama:</strong> {{ $penawaran->created_by ?? 'Nama Kontak' }}</p>
                    <p style="margin: 2px 0;"><strong>Telepon:</strong> {{ $penawaran->kontak_telepon ?? '+628-123-456' }}</p>
                    <p style="margin: 2px 0;"><strong>Email:</strong> {{ $penawaran->kontak_email ?? 'ikawati@proenergi.co.id' }}</p>
                </div>
            </div>
        </div>
        

        {{-- Footer --}}
        <div class="footer">
            PT. Tri Daya Selaras &bull; Gedung Graha Irama Lantai 6 unit G, Jln. HR Rasuna Said Blok X1 Kav 1-2.
             &bull; Telp. +021 5289 2321, Fax +021 5289 2310
        </div>
    </div>
</body>
</html>
