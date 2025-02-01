<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supplier extends Model
{
    protected $table = 'suppliers';

    protected $fillable = [
        'store_id',
        'name',
        'email',
        'phone',
        'address',
        'created_by',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->select('id', 'name', 'email');

    }
    //    public function purchases(){
    //        return $this->hasMany(Purchase::class);
    //    }
}
