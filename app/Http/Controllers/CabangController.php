<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;

class CabangController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $query = Cabang::query();

        if ($search = $request->query('search')) {
            $query->where('nama_cabang', 'like', "%{$search}%");
        }

        $data = $query->paginate($perPage);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_cabang'     => 'required|string|max:150|unique:cabangs,nama_cabang',
            'is_active'       => 'boolean',
            'created_by'      => 'nullable|string',
        ]);

        $data['created_time'] = now();
        $cabang = Cabang::create($data);

        return response()->json($cabang, 201);
    }

    public function show($id)
    {
        $cabang = Cabang::findOrFail($id);
        return response()->json($cabang);
    }

    public function update(Request $request, $id)
    {
        $cabang = Cabang::findOrFail($id);

        $data = $request->validate([
            'nama_cabang'     => "required|string|max:150|unique:cabangs,nama_cabang,{$id},id_cabang",
            'is_active'       => 'boolean',
            'lastupdate_by'   => 'nullable|string',
        ]);

        $data['lastupdate_time'] = now();
        $cabang->update($data);

        return response()->json($cabang);
    }

    public function destroy($id)
    {
        Cabang::destroy($id);
        return response()->json(null, 204);
    }
}
