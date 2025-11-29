<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prices extends Model
{
    use HasFactory;

    protected $table = 'prices';
    protected $primaryKey = 'ID';
    public $timestamps = true; // true untuk menggunakan created_at & updated_at

    protected $fillable = [
        'KODE',
        'KETERANGAN',
        'DARI',
        'SAMPAI',
        'RUTE',
        'HARGA',
        'HV',
        'HKG',
        'HBOK',
        'USER',
        'USEREDIT',
        'KUNCI',
        'HG',
        'JENIS'
        // created_at & updated_at otomatis, tidak perlu dimasukkan fillable
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
