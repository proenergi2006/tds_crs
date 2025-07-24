<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // GET /api/customers
    public function index(Request $request)
    {
        // ambil user yang lagi login
        $userId = $request->user()->id;
        

        $q = Customer::with(['user', 'provinsi', 'kabupaten'])
        ->where('id_user', $userId);
        ;

        if ($s = $request->query('search')) {
            $q->where(function($q) use ($s) {
                $q->where('email', 'like', "%{$s}%")
                  ->orWhere('nama_perusahaan', 'like', "%{$s}%");
            });
        }

        $perPage = $request->query('per_page', 10);
        return response()->json($q->paginate($perPage));
    }

    // POST /api/customers
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_user'           => 'required|exists:users,id',
            'email'             => 'required|email|unique:customers,email',
            'id_provinsi'       => 'required|exists:provinsis,id_provinsi',
            'id_kabupaten'      => 'required|exists:kabupatens,id_kabupaten',
            'postal_code'       => 'nullable|string|max:20',
            'telepon'           => 'nullable|string|max:30',
            'jenis_customer'    => 'nullable|string|max:50',
            'nama_perusahaan'   => 'nullable|string|max:255',
            'alamat_perusahaan' => 'nullable|string',
            'fax'               => 'nullable|string|max:30',
            'is_active'         => 'sometimes|boolean',
        ]);

        $data['created_time'] = now();
        $data['created_by']   = $request->user()->name;

        $customer = Customer::create($data);

        return response()->json($customer, 201);
    }

    // GET /api/customers/{id}
    public function show($id)
    {
        $customer = Customer::with(['user', 'provinsi', 'kabupaten'])
                            ->findOrFail($id);
        return response()->json($customer);
    }

    // PUT/PATCH /api/customers/{id}
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'id_user'           => 'required|exists:users,id',
            'email'             => "required|email|unique:customers,email,{$id},id_customer",
            'id_provinsi'       => 'required|exists:provinsis,id_provinsi',
            'id_kabupaten'      => 'required|exists:kabupatens,id_kabupaten',
            'postal_code'       => 'nullable|string|max:20',
            'telepon'           => 'nullable|string|max:30',
            'jenis_customer'    => 'nullable|string|max:50',
            'nama_perusahaan'   => 'nullable|string|max:255',
            'alamat_perusahaan' => 'nullable|string',
            'fax'               => 'nullable|string|max:30',
            'is_active'         => 'sometimes|boolean',
        ]);

        $data['lastupdate_time'] = now();
        $data['lastupdate_by']   = $request->user()->name;

        $customer = Customer::findOrFail($id);
        $customer->update($data);

        return response()->json($customer);
    }

    // DELETE /api/customers/{id}
    public function destroy($id)
    {
        Customer::destroy($id);
        return response()->json(null, 204);
    }
}
