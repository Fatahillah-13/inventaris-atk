<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemDivisionStock extends Model
{
    protected $fillable = [
        'item_id',
        'division_id',
        'stok_terkini',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}
