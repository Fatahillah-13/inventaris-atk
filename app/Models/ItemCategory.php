<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    protected $fillable = [
        'kode',
        'nama',
        'is_active',
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'category_id');
    }
}
