<?php

namespace App\Http\Controllers;

use App\Models\Penawaran;
use App\Models\PenawaranItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PDF;

class PenawaranController extends Controller
{
    /**
     * GET /api/penawarans
     * Menampilkan list penawaran (dengan pagination).
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $search  = $request->query('search');

        $query = Penawaran::with(['customer', 'cabang', 'items.produk']);

        if ($search) {
            $query->where('nomor_penawaran', 'like', "%{$search}%")
                  ->orWhere('kepada', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
        }

        $data = $query->orderBy('created_at', 'desc')
                      ->paginate($perPage);

        return response()->json($data);
    }

    public function previewPdf($id)
    {
        // Ambil data penawaran beserta relasi yang dibutuhkan
        $penawaran = Penawaran::with(['customer', 'cabang', 'items.produk.ukuran'])
            ->findOrFail($id);

        // Opsional: ambil data profil perusahaan (logo, nama, alamat)
        $company = [
            'nama_perusahaan' => config('app.name'),
            'alamat'          => 'Alamat Perusahaan Anda',
            'telepon'         => '021-xxxxxxx',
            'fax'             => '021-xxxxxxx',
            'logo_path'       => null, // misal 'logo.png' di storage/app/public
        ];

        // Lakukan render view PDF
        $pdf = PDF::loadView('penawaran.pdf', compact('penawaran', 'company'));
        
        // Atur ukuran kertas (misal A4, portrait)
        $pdf->setPaper('a4', 'portrait');

        // Langsung stream ke browser (inline)
        return $pdf->stream("Quotation-{$penawaran->nomor_penawaran}.pdf");
    }

    public function previewPdflub($id)
    {
        // Ambil data penawaran beserta relasi yang dibutuhkan
        $penawaran = Penawaran::with(['customer', 'cabang', 'items.produk.ukuran'])
            ->findOrFail($id);

        // Opsional: ambil data profil perusahaan (logo, nama, alamat)
        $company = [
            'nama_perusahaan' => config('app.name'),
            'alamat'          => 'Alamat Perusahaan Anda',
            'telepon'         => '021-xxxxxxx',
            'fax'             => '021-xxxxxxx',
            'logo_path'       => null, // misal 'logo.png' di storage/app/public
        ];

        // Lakukan render view PDF
        $pdf = PDF::loadView('penawaran.pdflub', compact('penawaran', 'company'));
        
        // Atur ukuran kertas (misal A4, portrait)
        $pdf->setPaper('a4', 'portrait');

        // Langsung stream ke browser (inline)
        return $pdf->stream("Quotation-{$penawaran->nomor_penawaran}.pdf");
    }

    /**
     * GET /api/penawarans/{id}
     */
    public function show($id)
    {
        $penawaran = Penawaran::with([
            'customer',
            'cabang',
            'items.produk.jenis',
            'items.produk.ukuran.satuan'
        ])->findOrFail($id);
    
        return response()->json($penawaran);
    }

    /**
     * POST /api/penawarans
     */
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'id_customer'          => 'required|exists:customers,id_customer',
        'id_cabang'            => 'required|exists:cabangs,id_cabang',
        'masa_berlaku'         => 'required|date',
        'sampai_dengan'        => 'required|date|after_or_equal:masa_berlaku',
        'items'                => 'required|array|min:1',
        'items.*.id_produk'    => 'required|exists:produks,id_produk',
        'items.*.volume_order' => 'required|numeric|min:0',
        'items.*.harga_tebus'  => 'required|numeric|min:0',
        'tipe_pembayaran'      => 'nullable|string|max:100',
        'order_method'         => 'nullable|string|max:100',
        'toleransi_penyusutan' => 'nullable|numeric',
        'lokasi_pengiriman'    => 'nullable|string|max:255',
        'metode'               => 'nullable|string|max:100',
        'refund'               => 'nullable|numeric',
        'other_cost'           => 'nullable|numeric',
        'perhitungan'          => 'nullable|string',
        'keterangan'           => 'nullable|string',
        'catatan'              => 'nullable|string',
        'syarat_ketentuan'     => 'nullable|string',
        'discount'             => 'nullable|numeric|min:0',
        'oat'                  => 'nullable|numeric|min:0',
        'jenis_penawaran'      => 'nullable|string|max:100',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $data = $validator->validated();

    // Nomor otomatis
    $prefix = 'PNW-' . date('Ymd') . '-';
    $last = Penawaran::where('nomor_penawaran', 'like', "{$prefix}%")
        ->orderBy('nomor_penawaran', 'desc')->first();
    $number = $last ? ((int) substr($last->nomor_penawaran, strlen($prefix)) + 1) : 1;
    $data['nomor_penawaran'] = $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);

    // Hitung harga
    $subtotal = 0;
    foreach ($data['items'] as $it) {
        $subtotal += $it['volume_order'] * $it['harga_tebus'];
    }

    $diskon_persen = $data['discount'] ?? 0;
    $harga_setelah_diskon = $subtotal - ($subtotal * $diskon_persen / 100);

    $oat_rp = $data['oat'] ?? 0;
    $total_volume = array_sum(array_column($data['items'], 'volume_order'));
    $total_oat = $oat_rp * $total_volume;

    $ppn11 = round($harga_setelah_diskon * 0.11, 2);
    $total_with_oat = $harga_setelah_diskon + $total_oat + $ppn11;

    $data['subtotal'] = $subtotal;
    $data['harga_tebus_setelah_diskon'] = $harga_setelah_diskon;
    $data['ppn11'] = $ppn11;
    $data['total'] = $harga_setelah_diskon + $ppn11;
    $data['total_with_oat'] = $total_with_oat;

    $data['created_at'] = now();
    $data['created_by'] = $request->user()->name ?? null;

    DB::beginTransaction();
    try {
        $penawaran = Penawaran::create($data);

        foreach ($data['items'] as $it) {
            PenawaranItem::create([
                'id_penawaran' => $penawaran->id_penawaran,
                'id_produk' => $it['id_produk'],
                'volume_order' => $it['volume_order'],
                'harga_tebus' => $it['harga_tebus'],
                'jumlah_harga' => $it['volume_order'] * $it['harga_tebus'],
            ]);
        }

        DB::commit();
        $penawaran->load(['customer', 'cabang', 'items.produk']);
        return response()->json($penawaran, 201);
    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json(['message' => 'Gagal menyimpan penawaran', 'error' => $e->getMessage()], 500);
    }
}


    /**
     * PUT /api/penawarans/{id}
     */
    public function update(Request $request, $id)
    {
        $penawaran = Penawaran::findOrFail($id);
    
        $validator = Validator::make($request->all(), [
            'id_customer'          => 'required|exists:customers,id_customer',
            'id_cabang'            => 'required|exists:cabangs,id_cabang',
            'masa_berlaku'         => 'required|date',
            'sampai_dengan'        => 'required|date|after_or_equal:masa_berlaku',
            'items'                => 'required|array|min:1',
            'items.*.id_produk'    => 'required|exists:produks,id_produk',
            'items.*.volume_order' => 'required|numeric|min:0',
            'items.*.harga_tebus'  => 'required|numeric|min:0',
            'tipe_pembayaran'      => 'nullable|string|max:100',
            'order_method'         => 'nullable|string|max:100',
            'toleransi_penyusutan' => 'nullable|numeric',
            'lokasi_pengiriman'    => 'nullable|string|max:255',
            'metode'               => 'nullable|string|max:100',
            'refund'               => 'nullable|numeric',
            'other_cost'           => 'nullable|numeric',
            'perhitungan'          => 'nullable|string',
            'keterangan'           => 'nullable|string',
            'catatan'              => 'nullable|string',
            'syarat_ketentuan'     => 'nullable|string',
            'discount'             => 'nullable|numeric|min:0',
            'oat'                  => 'nullable|numeric|min:0',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $data = $validator->validated();
    
        $subtotal = 0;
        foreach ($data['items'] as $it) {
            $subtotal += $it['volume_order'] * $it['harga_tebus'];
        }
    
        $diskon_persen = $data['discount'] ?? 0;
        $harga_setelah_diskon = $subtotal - ($subtotal * $diskon_persen / 100);
    
        $oat_rp = $data['oat'] ?? 0;
        $total_volume = array_sum(array_column($data['items'], 'volume_order'));
        $total_oat = $oat_rp * $total_volume;
    
        $ppn11 = round($harga_setelah_diskon * 0.11, 2);
        $total_with_oat = $harga_setelah_diskon + $total_oat + $ppn11;
    
        $data['subtotal'] = $subtotal;
        $data['harga_tebus_setelah_diskon'] = $harga_setelah_diskon;
        $data['ppn11'] = $ppn11;
        $data['total'] = $harga_setelah_diskon + $ppn11;
        $data['total_with_oat'] = $total_with_oat;
    
        $data['updated_at'] = now();
        $data['updated_by'] = $request->user()->name ?? null;
    
        DB::beginTransaction();
        try {
            $penawaran->update($data);
    
            PenawaranItem::where('id_penawaran', $penawaran->id_penawaran)->delete();
    
            foreach ($data['items'] as $it) {
                PenawaranItem::create([
                    'id_penawaran' => $penawaran->id_penawaran,
                    'id_produk' => $it['id_produk'],
                    'volume_order' => $it['volume_order'],
                    'harga_tebus' => $it['harga_tebus'],
                    'jumlah_harga' => $it['volume_order'] * $it['harga_tebus'],
                ]);
            }
    
            DB::commit();
            $penawaran->load(['customer', 'cabang', 'items.produk']);
            return response()->json($penawaran);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal update penawaran', 'error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * DELETE /api/penawarans/{id}
     */
    public function destroy($id)
    {
        $penawaran = Penawaran::findOrFail($id);
        // Otomatis item akan ke‐hapus karena kita set onDelete('cascade') di migration
        $penawaran->delete();

        return response()->json(null, 204);
    }
}
