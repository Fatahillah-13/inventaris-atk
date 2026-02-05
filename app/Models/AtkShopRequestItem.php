<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AtkShopRequestItem extends Model
{
    protected $table = 'atk_shop_request_items';

    protected $fillable = [
        'atk_shop_request_id',
        'item_id',
        'qty',
        'status',
        'arrived_at',
        'taken_at',
    ];

    public function atkShopRequest(): BelongsTo
    {
        return $this->belongsTo(AtkShopRequest::class, 'atk_shop_request_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
