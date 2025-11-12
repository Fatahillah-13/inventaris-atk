<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtkRequest extends Model
{
    protected $fillable = [
        'kode_request',
        'item_id',
        'user_id',
        'peminta',
        'departemen',
        'jumlah',
        'tanggal',
        'keterangan',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        // user internal yang memproses (boleh null)
        return $this->belongsTo(User::class);
    }
}
