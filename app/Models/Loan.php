<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'kode_loan',
        'item_id',
        'user_id',
        'peminjam',
        'departemen',
        'jumlah',
        'tanggal_pinjam',
        'tanggal_rencana_kembali',
        'tanggal_kembali',
        'status',
        'keterangan',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        // boleh null
        return $this->belongsTo(User::class);
    }
}
