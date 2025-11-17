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

    public function divisionStocks()
    {
        return $this->hasMany(\App\Models\ItemDivisionStock::class);
    }

    public function divisions()
    {
        return $this->belongsToMany(\App\Models\Division::class, 'item_division_stocks')
            ->withPivot('stok_terkini')
            ->withTimestamps();
    }
}
