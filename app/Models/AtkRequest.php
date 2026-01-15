<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Legacy ATK Request model for public form workflow
 */
class AtkRequest extends Model
{
    protected $table = 'atk_requests';
    
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

    protected $casts = [
        'tanggal' => 'date',
        'approved_at' => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        // user internal yang memproses (boleh null)
        return $this->belongsTo(User::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
