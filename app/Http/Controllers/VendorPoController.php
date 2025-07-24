<?php

namespace App\Http\Controllers;

use App\Models\VendorPo;
use Illuminate\Http\Request;
use PDF;
// **Tambahkan ini:**
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VendorPoController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $q = VendorPo::with(['vendor', 'terminal', 'produks.produk']);

        if ($search = $request->query('search')) {
            $q->where('nomor_po', 'like', "%{$search}%")
              ->orWhere('keterangan', 'like', "%{$search}%");
        }

        return response()->json($q->paginate($perPage));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_vendor'     => 'required|exists:vendors,id_vendor',
            'id_terminal'   => 'required|exists:terminals,id_terminal',
            'nomor_po'      => 'required|string|max:255',
            'tanggal_inven' => 'required|date',
            'kd_tax'        => 'required|string|max:10',
            'terms'         => 'required|string|max:10',
            'terms_day'     => 'required|integer',
            'subtotal'      => 'required|numeric',
            'ppn11'         => 'required|numeric',
            'total_order'   => 'required|numeric',
            'keterangan'    => 'nullable|string',
            'terms_condition'    => 'nullable|string',
            'disposisi_po'  => 'nullable|integer',
        ]);

        $data['created_time'] = now();
        $data['created_by']   = $request->user()->name;

        $po = VendorPo::create($data);

        return response()->json($po, 201);
    }

    public function show($id)
    {
        $po = VendorPo::with(['vendor', 'terminal', 'produks.produk', 'produks.produk.ukuran.satuan', 'produks.produk.jenis'])
                      ->findOrFail($id);

        return response()->json($po);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'id_vendor'     => 'required|exists:vendors,id_vendor',
            'id_terminal'   => 'required|exists:terminals,id_terminal',
            'nomor_po'      => 'required|string|max:255',
            'tanggal_inven' => 'required|date',
            'kd_tax'        => 'required|string|max:10',
            'terms'         => 'required|string|max:10',
            'terms_day'     => 'required|integer',
            'subtotal'      => 'required|numeric',
            'ppn11'         => 'required|numeric',
            'total_order'   => 'required|numeric',
            'keterangan'    => 'nullable|string',
            'terms_condition'    => 'nullable|string',
            'disposisi_po'  => 'nullable|integer',
        ]);

        $data['lastupdate_time'] = now();
        $data['lastupdate_by']   = $request->user()->name;

        $po = VendorPo::findOrFail($id);
        $po->update($data);

        return response()->json($po);
    }

    public function approve(Request $request, $id)
{
    $po = VendorPo::findOrFail($id);
    $po->disposisi_po      = 1;
    $po->lastupdate_time   = now();
    $po->lastupdate_by     = $request->user()->name;
    $po->save();

    return response()->json($po);
}

public function preview($id)
{
    $po = VendorPo::with(['vendor','terminal','produks.produk'])->findOrFail($id);


    // Render PDF
    $pdf = PDF::loadView('vendorpos.preview', compact('po'))
              ->setOptions(['isRemoteEnabled' => true])
              ->setPaper('a4', 'portrait');

    return $pdf->stream("PO-{$po->nomor_po}.pdf");
}

/**
 * Endpoint publik untuk menampilkan data PO (JSON).
 */
public function publicShow($id)
{
    $po = VendorPo::with(['vendor','terminal','produks.produk'])
                  ->findOrFail($id);

    return response()->json($po);
}


    public function destroy($id)
    {
        VendorPo::destroy($id);
        return response()->json(null, 204);
    }

    
}
