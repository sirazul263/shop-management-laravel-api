<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sell extends Model
{
    protected $table = 'sells';

    protected $fillable = [
        'store_id',
        'user_id',
        'total',
        'payment_method',
        'payment_status',
        'name',
        'phone',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SellItem::class);
    }
}
