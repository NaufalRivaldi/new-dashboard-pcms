<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportedActiveStudentEducationDetail extends Model
{
    protected $fillable = [
        'total',
        'lesson_id',
        'imported_active_studen_education_id',
    ];

    public function education(): BelongsTo
    {
        return $this->belongsTo(Education::class);
    }
}
