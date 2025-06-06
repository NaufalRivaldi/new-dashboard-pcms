<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportedActiveStudent extends Model
{
    protected $fillable = [
        'month',
        'year',
        'total',
        'branch_id',
        'user_id',
    ];

    protected $casts = [
        'total' => 'integer',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(ImportedActiveStudentDetail::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
