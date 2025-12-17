<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $table = 'driver';

    protected $fillable = [
        'KODE',
        'NAMA',
        'ALAMAT',
        'PHONE',
        'MULAI',
    ];

    protected $casts = [
        'MULAI' => 'date'
    ];
}
