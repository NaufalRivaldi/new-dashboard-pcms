<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportedActiveStudentEducation extends Model
{
    protected $fillable = [
        'month',
        'year',
        'total',
        'branch_id',
        'user_id',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(ImportedActiveStudentEducationDetail::class);
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
