<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    // primary key custom
    protected $primaryKey = 'id_cabang';

    // tidak pakai timestamps default
    public $timestamps = false;

    protected $fillable = [
        'nama_cabang',
        'is_active',
        'created_time',
        'created_by',
        'lastupdate_time',
        'lastupdate_by',
    ];
}
