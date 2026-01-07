<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OldAtkRequest extends Model
{
    protected $table = 'old_atk_requests';
    protected $fillable = [
        'kode_request',
        'item_id',
        'user_id',
        'division_id',
        'peminta',
        'departemen',
        'jumlah',
        'tanggal',
        'keterangan',
        'status',
        'approved_by',
        'approved_at',
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

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
