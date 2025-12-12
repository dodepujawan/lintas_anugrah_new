<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricedingincus extends Model
{
    use HasFactory;

    protected $table = 'pricedingincus';
    protected $primaryKey = 'id';
    public $timestamps = true; // true untuk menggunakan created_at & updated_at

    protected $fillable = [
        'KODECUS',
        'KODEDGN',
        'KODE',
        'PERIODE',
        'PLAT',
        'JENIS',
        'ITEM',
        'HARGA',
        'USER',
        'USEREDIT',
        'KUNCI'
        // created_at & updated_at otomatis, tidak perlu dimasukkan fillable
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
