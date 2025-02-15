<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportedLeaveStudent extends Model
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

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
