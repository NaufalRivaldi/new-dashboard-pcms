<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'code',
        'name',
        'status',
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

    public function importedFees(): HasMany
    {
        return $this->hasMany(ImportedFee::class);
    }

    public function summaries(): HasMany
    {
        return $this->hasMany(Summary::class);
    }

    public function scopeActive(Builder $query)
    {
        $query->where('status', Status::Active->value);
    }

    public function scopeInactive(Builder $query)
    {
        $query->where('status', Status::Inactive->value);
    }
}
