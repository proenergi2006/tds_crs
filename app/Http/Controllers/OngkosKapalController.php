<?php

namespace App\Http\Controllers;

use App\Models\OngkosKapal;
use App\Models\OngkosDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OngkosKapalController extends Controller
{
    public function index(Request $request)
{
    try {
        $data = OngkosKapal::with(['transportir', 'angkutWilayah', 'details.volume'])
            ->latest()
            ->paginate($request->get('per_page', 10));

        return response()->json($data);
    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'Error: ' . $e->getMessage(),
            'trace' => $e->getTrace()
        ], 500);
    }
}


    public function store(Request $request)
    {
        $data = $request->validate([
            'id_transportir' => 'required|exists:transportirs,id',
            'id_angkut_wilayah' => 'required|exists:wilayah_angkuts,id',
            'details' => 'required|array',
            'details.*.id_volume' => 'required|exists:volumes,id_volume',
            'details.*.oa' => 'required|numeric|min:0',
        ]);

        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        $ongkos = OngkosKapal::create($data);

        foreach ($data['details'] as $detail) {
            $ongkos->details()->create($detail);
        }

        return response()->json($ongkos->load(['details.volume']), 201);
    }

    public function show($id)
    {
        $ongkos = OngkosKapal::with(['details.volume'])->findOrFail($id);
        return response()->json($ongkos);
    }

    public function update(Request $request, $id)
    {
        $ongkos = OngkosKapal::findOrFail($id);

        $data = $request->validate([
            'id_transportir' => 'required|exists:transportirs,id',
            'id_angkut_wilayah' => 'required|exists:wilayah_angkuts,id',
            'details' => 'required|array',
            'details.*.id_volume' => 'required|exists:volumes,id_volume',
            'details.*.oa' => 'required|numeric|min:0',
        ]);

        $data['updated_by'] = Auth::id();
        $ongkos->update($data);

        $ongkos->details()->delete();
        foreach ($data['details'] as $detail) {
            $ongkos->details()->create($detail);
        }

        return response()->json($ongkos->load(['details.volume']));
    }

    public function destroy($id)
    {
        $ongkos = OngkosKapal::findOrFail($id);
        $ongkos->details()->delete();
        $ongkos->delete();

        return response()->json(['message' => 'Data ongkos kapal berhasil dihapus']);
    }
}
