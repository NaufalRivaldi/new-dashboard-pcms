<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportedFeeDetail extends Model
{
    protected $fillable = [
        'type',
        'payer_name',
        'nominal',
        'lesson_id',
        'imported_fee_id',
    ];

    protected $casts = [
        'nominal' => 'double',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
