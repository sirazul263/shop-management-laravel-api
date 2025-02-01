<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'store_id',
        'user_id',
        'type',
        'operation',
        'remark',
    ];

    public function user(): BelongsTo
    {

        return $this->belongsTo(User::class);
    }
}
