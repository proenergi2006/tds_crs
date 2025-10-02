<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\CustomerLogistik;
use App\Models\CustomerPayment;
use App\Models\CustomerAdminArnya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    // GET /api/customers
    public function index(Request $request)
    {
        // ambil user yang lagi login
        $userId = $request->user()->id;
        

        // App\Http\Controllers\CustomerController@index
$q = Customer::query()
->with(['user','provinsi','kabupaten','cabang'])
->where('id_user', $userId)
->withExists(['lcr as has_lcr'])
->withCount(['penawarans as jumlah_penawaran']); // ← ini yang ngasih count

        

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

        $customer = null;

        DB::transaction(function () use (&$customer, $data) {
            // 1) buat customer utama
            $customer = Customer::create($data);

            // 2) seed tabel contact (PK = id_customer)
            CustomerContact::firstOrCreate(
                ['id_customer' => $customer->id_customer],
                [
                    'pic_decision_telp'   => '',
                    'pic_decision_mobile' => '',
                    'pic_ordering_telp'   => '',
                    'pic_ordering_mobile' => '',
                    'pic_billing_telp'    => '',
                    'pic_billing_mobile'  => '',
                    'pic_invoice_telp'    => '',
                    'pic_invoice_mobile'  => '',
                ]
            );

            // 3) seed tabel logistik (banyak kolom NOT NULL → kasih default minimal)
            CustomerLogistik::firstOrCreate(
                ['id_customer' => $customer->id_customer],
                [
                    'logistik_area'       => '',   // text NOT NULL
                    'logistik_bisnis'     => '',   // text NOT NULL
                    'logistik_env'        => 0,
                    'logistik_storage'    => 0,
                    'logistik_hour'       => 0,
                    'logistik_volume'     => 0,
                    'logistik_quality'    => 0,
                    'logistik_truck'      => 0,
                    'desc_stor_fac'       => '',   // text NOT NULL
                    'desc_condition'      => '',   // text NOT NULL
                    // kolom lain yg nullable biarkan null (otomatis)
                ]
            );

            // 4) seed tabel payment (beberapa kolom NOT NULL → default minimal)
            CustomerPayment::firstOrCreate(
                ['id_customer' => $customer->id_customer],
                [
                    'telp_billing'        => '',   // NOT NULL
                    'fax_billing'         => '',   // NOT NULL
                    'payment_schedule'    => 0,    // NOT NULL
                    'payment_method'      => 0,    // NOT NULL
                    'invoice'             => 0,    // NOT NULL
                    'ket_extra'           => '',   // NOT NULL (text)
                ]
            );

            // 5) seed admin_arnya (PK autoincrement → cukup id_customer)
            CustomerAdminArnya::firstOrCreate(
                ['id_customer' => $customer->id_customer],
                [
                    'not_yet'     => 0,
                    'ov_up_07'    => 0,
                    'ov_under_30' => 0,
                    'ov_under_60' => 0,
                    'ov_under_90' => 0,
                    'ov_up_90'    => 0,
                ]
            );
        });

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
