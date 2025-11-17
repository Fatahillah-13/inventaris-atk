<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = ['nama', 'kode'];

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_division_stocks')
            ->withPivot('stok_terkini')
            ->withTimestamps();
    }
}
