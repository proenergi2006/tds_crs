<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Purchase Order {{ $po->nomor_po }}</title>
  <style>
    /* Reset & Global */
    @page { margin: 30px; }
    body { margin:0; padding:0; font-family:'DejaVu Sans', sans-serif; color:#333; font-size:12px; }
    .container { width:100%; }

    /* Header */
    .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
    .logo img { max-height:60px; }
    .company-details { text-align:right; }
    .company-details h2 { margin:0; font-size:18px; color:#2C3E50; }
    .company-details p { margin:2px 0; font-size:10px; color:#555; }

    /* Divider */
    .divider { border-top:2px solid #2C3E50; margin:10px 0; }

    /* Title */
    .title { text-align:center; margin:10px 0 20px; }
    .title h1 { margin:0; font-size:20px; letter-spacing:2px; color:#2C3E50; }
    .title h3 { margin:5px 0 0; font-size:14px; font-weight:normal; color:#555; }

    /* PO Info */
    .po-info { width:100%; margin-bottom:20px; }
    .po-info td { padding:4px 8px; vertical-align:top; }
    .po-info .label { font-weight:bold; width:120px; color:#2C3E50; }

    /* Table */
    .items-table { width:100%; border-collapse:collapse; margin-bottom:20px; }
    .items-table th, .items-table td {
      border:1px solid #DDD;
      padding:8px;
    }
    .items-table th {
      background:#F7F9FA;
      color:#2C3E50;
      text-transform:uppercase;
      font-size:11px;
    }
    .items-table tbody tr:nth-child(even) {
      background:#FCFCFC;
    }

    /* Totals */
    .totals { width:40%; float:right; border-collapse:collapse; margin-bottom:40px; }
    .totals td { padding:6px 8px; }
    .totals .label { font-weight:bold; color:#2C3E50; }

    /* Signatures */
    .signatures { width:100%; display:flex; justify-content:space-between; margin-top:60px; }
    .sign-box { width:30%; text-align:center; }
    .sign-box .line { margin-top:60px; border-top:1px dashed #999; }

    /* Footer */
    .footer { position:fixed; bottom:0; left:0; right:0; text-align:center; font-size:10px; color:#999; }
  </style>
</head>
<body>
  <div class="container">
    {{-- Header --}}
    <div class="header">
      <div class="logo">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo">
      </div>
      <div class="company-details">
        <h2>{{ config('app.name') }}</h2>
        <p>{{ config('company.address') }}</p>
        <p>Phone: {{ config('company.phone') }}</p>
        <p>Email: {{ config('company.email') }}</p>
      </div>
    </div>

    <div class="divider"></div>

    {{-- Title --}}
    <div class="title">
      <h1>PURCHASE ORDER</h1>
      <h3>No: {{ $po->nomor_po }}</h3>
    </div>

    {{-- PO Info --}}
    <table class="po-info">
      <tr>
        <td class="label">Tanggal PO</td>
        <td>{{ \Carbon\Carbon::parse($po->tanggal_inven)->translatedFormat('d F Y') }}</td>
      </tr>
      <tr>
        <td class="label">Vendor</td>
        <td>{{ $po->vendor->nama_vendor }}</td>
      </tr>
      <tr>
        <td class="label">Terminal</td>
        <td>{{ $po->terminal->nama_terminal }}</td>
      </tr>
      <tr>
        <td class="label">Terms</td>
        <td>{{ $po->terms }} ({{ $po->terms_day }} hari)</td>
      </tr>
      <tr>
        <td class="label">Keterangan</td>
        <td>{{ $po->keterangan ?? '-' }}</td>
      </tr>
    </table>

    {{-- Item Details --}}
    <table class="items-table">
      <thead>
        <tr>
          <th>Produk</th>
          <th class="text-right">Volume</th>
          <th class="text-right">Harga Tebus</th>
          <th class="text-right">Jumlah</th>
        </tr>
      </thead>
      <tbody>
        @foreach($po->produks as $item)
        <tr>
          <td>{{ $item->produk->nama_produk }}</td>
          <td class="text-right">{{ number_format($item->volume_po,2,',','.') }}</td>
          <td class="text-right">{{ number_format($item->harga_tebus,2,',','.') }}</td>
          <td class="text-right">{{ number_format($item->jumlah_harga,2,',','.') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    {{-- Totals --}}
    <table class="totals">
      <tr>
        <td class="label">Subtotal</td>
        <td class="text-right">{{ number_format($po->subtotal,2,',','.') }}</td>
      </tr>
      <tr>
        <td class="label">PPN 11%</td>
        <td class="text-right">{{ number_format($po->ppn11,2,',','.') }}</td>
      </tr>
      <tr>
        <td class="label">Grand Total</td>
        <td class="text-right">{{ number_format($po->total_order,2,',','.') }}</td>
      </tr>
    </table>

 
    @php
        // pisahkan dolar dan sen
        $total     = number_format($po->total_order, 2, '.', '');
        [$dols, $cents] = explode('.', $total);
        // buat formatter
        $fmt       = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $dolsText  = $fmt->format((int)$dols);
        $centsText = (int)$cents > 0
            ? ' and ' . $fmt->format((int)$cents) . ' cents'
            : '';
        // kapital huruf pertama
        $amountWords = ucfirst($dolsText) . ' dollars' . $centsText;
    @endphp
    <div style="
      border:1px solid #DDD;
      padding:8px 12px;
      width:40%;
      margin-top:10px;
      font-style:italic;
      color:#555;
    ">
      {{ $amountWords }}
    </div>

    
    {{-- Footer --}}
    <div class="footer">
      Halaman <script type="text/php">
        if (isset($pdf)) {
          $pdf->page_text(540, 820, "Halaman {PAGE_NUM} dari {PAGE_COUNT}", null, 8);
        }
      </script>
    </div>
  </div>
</body>
</html>
