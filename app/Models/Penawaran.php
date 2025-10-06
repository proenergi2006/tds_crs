<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penawaran extends Model
{
    protected $table      = 'penawarans';
    protected $primaryKey = 'id_penawaran';
    public $timestamps    = false; // gunakan manual timestamp

    protected $fillable = [
        'id_customer',
        'id_cabang',
        'nomor_penawaran',
        'qr_code',
        'masa_berlaku',
        'sampai_dengan',
        'subtotal',
        'ppn11',
        'total',
        'kepada',
        'jabatan',
        'telepon',
        'nama',
        'alamat',
        'fax',
        'tipe_pembayaran',
        'order_method',
        'toleransi_penyusutan',
        'lokasi_pengiriman',
        'metode',
        'refund',
        'other_cost',
        'perhitungan',
        'keterangan',
        'catatan',
        'syarat_ketentuan',
        'discount',
        'harga_tebus_setelah_diskon',
        'total_with_oat',
        'oat',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'jenis_penawaran',
        'status', // tambahkan ini
        'disposisi_penawaran',
        'bm_result',
        'bm_tanggal',
        'catatan_verifikasi',
        'catatan_om',
        'abrasi',
        'user_id', // atau 'id_user' kalau pakai opsi B
    ];

    /******** Relasi ********/

    // Penawaran belongsTo Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer', 'id_customer');
    }

    // Penawaran belongsTo Cabang
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang', 'id_cabang');
    }

    // Penawaran hasMany PenawaranItem
    public function items()
    {
        return $this->hasMany(PenawaranItem::class, 'id_penawaran', 'id_penawaran');
    }

    public function user()
    {
        // pakai foreign key user_id (kalau kamu pakai id_user, ganti argumennya)
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    
}
