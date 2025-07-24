<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $primaryKey = 'id_customer';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'email',
        'id_provinsi',
        'id_kabupaten',
        'postal_code',
        'telepon',
        'jenis_customer',
        'nama_perusahaan',
        'alamat_perusahaan',
        'fax',
        'created_time',
        'created_by',
        'lastupdate_time',
        'lastupdate_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'id_provinsi', 'id_provinsi');
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'id_kabupaten', 'id_kabupaten');
    }
}
