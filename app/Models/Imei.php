<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Imei extends Model

{
    protected $table = 'imei_numbers';
    protected $fillable = [
        'id',
        'iemi',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
