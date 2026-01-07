<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AtkRequestItem extends Model
{
    protected $fillable = [
        'atk_request_id',
        'item_id',
        'qty',
    ];

    public function atkRequest(): BelongsTo
    {
        return $this->belongsTo(AtkRequest::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
