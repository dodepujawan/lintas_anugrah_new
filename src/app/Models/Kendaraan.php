<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    protected $table = 'kendaraan';

    protected $fillable = [
        'kode',
        'nama',
        'plat',
        'jens',
        'fno_prk_b',
        'fno_prk_p',
        'fno_prk_s',
        'fno_prk_o',
        'fno_prk_m'
    ];
}
