<?php

namespace App\Http\Controllers;

use App\Models\ProdukHarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdukHargaController extends Controller
{
    public function index(Request $request)
    {
        $q = ProdukHarga::with(['cabang', 'produk.ukuran.satuan']);
    
        // filter by cabang jika param id_cabang dikirim
        if ($cabangId = $request->query('id_cabang')) {
            $q->where('id_cabang', $cabangId);
        }
    
        // filter by produk jika param id_produk dikirim
        if ($produkId = $request->query('id_produk')) {
            $q->where('id_produk', $produkId);
        }
    
        // search by nama produk atau nama cabang
        if ($s = $request->query('search')) {
            $q->where(function($q2) use ($s) {
                $q2->whereHas('produk', fn($q3) => $q3->where('nama_produk', 'like', "%{$s}%"))
                   ->orWhereHas('cabang', fn($q4) => $q4->where('nama_cabang', 'like', "%{$s}%"));
            });
        }
    
        $perPage = $request->query('per_page', 10);
        return response()->json($q->paginate($perPage));
    }

    public function check(Request $request)
    {
        $produkId = $request->query('produk_id');
        $tanggal = $request->query('tanggal');
    
        $harga = DB::table('produk_hargas')
            ->where('id_produk', $produkId)
            ->whereDate('periode_awal', '<=', $tanggal)
            ->whereDate('periode_akhir', '>=', $tanggal)
            ->orderByDesc('periode_akhir')
            ->first();
    
        return response()->json([
            'harga_price_list' => $harga?->harga_price_list ?? null,
            'found' => (bool) $harga,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'periode_awal'     => 'required|date',
            'periode_akhir'    => 'required|date|after_or_equal:periode_awal',
            'id_cabang'        => 'required|exists:cabangs,id_cabang',
            'id_produk'        => 'required|exists:produks,id_produk',
            'harga_price_list' => 'required|numeric',
            'harga_bm'         => 'required|numeric',
            'catatan'          => 'nullable|string',
        ]);
        $data['created_time'] = now();
        $data['created_by']   = $request->user()->name;
        $ph = ProdukHarga::create($data);
        return response()->json($ph, 201);
    }

    public function show($id)
    {
        $ph = ProdukHarga::with(['cabang', 'produk'])->findOrFail($id);
        return response()->json($ph);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'periode_awal'     => 'required|date',
            'periode_akhir'    => 'required|date|after_or_equal:periode_awal',
            'id_cabang'        => 'required|exists:cabangs,id_cabang',
            'id_produk'        => 'required|exists:produks,id_produk',
            'harga_price_list' => 'required|numeric',
            'harga_bm'         => 'required|numeric',
            'catatan'          => 'nullable|string',
        ]);
        $data['lastupdate_time'] = now();
        $data['lastupdate_by']   = $request->user()->name;
        $ph = ProdukHarga::findOrFail($id);
        $ph->update($data);
        return response()->json($ph);
    }

    public function destroy($id)
    {
        ProdukHarga::destroy($id);
        return response()->json(null, 204);
    }
}
