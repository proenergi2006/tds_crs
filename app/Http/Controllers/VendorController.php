<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $q = Vendor::query();

        if ($s = $request->query('search')) {
            $q->where('nama_vendor', 'like', "%{$s}%")
              ->orWhere('inisial', 'like', "%{$s}%");
        }

        $perPage = $request->query('per_page', 10);
        return response()->json($q->paginate($perPage));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'inisial'     => 'required|string|max:10',
            'catatan'     => 'nullable|string',
            'is_active'   => 'sometimes|boolean',
        ]);

        $data['created_time'] = now();
        $data['created_by']   = $request->user()->name;

        $vendor = Vendor::create($data);
        return response()->json($vendor, 201);
    }

    public function show($id)
    {
        $vendor = Vendor::findOrFail($id);
        return response()->json($vendor);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'inisial'     => 'required|string|max:10',
            'catatan'     => 'nullable|string',
            'is_active'   => 'sometimes|boolean',
        ]);

        $data['lastupdate_time'] = now();
        $data['lastupdate_by']   = $request->user()->name;

        $vendor = Vendor::findOrFail($id);
        $vendor->update($data);

        return response()->json($vendor);
    }

    public function destroy($id)
    {
        Vendor::destroy($id);
        return response()->json(null, 204);
    }
}
