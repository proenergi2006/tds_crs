<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $primaryKey = 'id_vendor';
    public $timestamps = false;

    protected $fillable = [
        'nama_vendor',
        'inisial',
        'catatan',
        'is_active',
        'created_time',
        'created_by',
        'lastupdate_time',
        'lastupdate_by',
    ];
}
