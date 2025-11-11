<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'item_category',
        'satuan',
        'stok_awal',
        'stok_terkini',
        'catatan',
    ];
}
