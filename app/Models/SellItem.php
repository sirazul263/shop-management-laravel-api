<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellItem extends Model
{
    protected $table = 'sell_items';

    protected $fillable = [
        'sell_id',
        'product_id',
        'quantity',
        'unit_amount',
        'total_amount',
        'imei',
    ];

    public function sell(): BelongsTo
    {
        return $this->belongsTo(Sell::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id'); // Make sure the column names match your table structure
    }
}
