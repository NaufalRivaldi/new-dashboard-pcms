<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'code',
        'name',
        'latitude',
        'longitude',
        'region_id',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(User::class, Ownership::class);
    }

    public function ownerships(): HasMany
    {
        return $this->hasMany(Ownership::class);
    }
}
