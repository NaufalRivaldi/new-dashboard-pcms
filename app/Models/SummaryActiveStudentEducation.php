<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SummaryActiveStudentEducation extends Model
{
    protected $fillable = [
        'total',
        'education_id',
        'summary_id',
    ];

    public function education(): BelongsTo
    {
        return $this->belongsTo(Education::class);
    }

    public function summary(): BelongsTo
    {
        return $this->belongsTo(Summary::class);
    }
}
