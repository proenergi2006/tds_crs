<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenawaranOA extends Model
{
    use HasFactory;

    protected $table = 'penawaran_oas';

    protected $fillable = [
        'id_penawaran',
        'tipe',
        'id_transportir',
        'id_angkut_wilayah',
        'id_volume',
        'oa',
    ];

    public function transportir()
    {
        return $this->belongsTo(Transportir::class, 'id_transportir');
    }

    public function wilayah()
    {
        return $this->belongsTo(WilayahAngkut::class, 'id_angkut_wilayah');
    }

    public function volume()
    {
        return $this->belongsTo(Volume::class, 'id_volume');
    }

    public function penawaran()
    {
        return $this->belongsTo(Penawaran::class, 'id_penawaran');
    }
}
