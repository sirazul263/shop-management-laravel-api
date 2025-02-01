<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Store extends Model
{
    public $incrementing = false; // Important: Disables auto-incrementing

    protected $keyType = 'string';

    protected $table = 'stores';

    protected $fillable = [
        'id',
        'name',
        'address',
        'phone',
        'image',
        'description',
        'status',
        'created_by',
    ];

    protected static function boot()
    {
        parent::boot();

        // Ensure UUID is generated before creating the store
        static::creating(function ($store) {
            if (empty($store->id)) {
                $store->id = Str::uuid()->toString(); // Generate UUID
            }
        });

        // Automatically assign user to the store after it's created
        static::created(function ($store) {
            if (Auth::check()) {
                $store->users()->attach(Auth::id());
            }
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->select('id', 'name', 'email');

    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'store_user')->withTimestamps();
    }
}
