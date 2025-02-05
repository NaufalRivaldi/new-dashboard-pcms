<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Summary extends Model
{
    protected $fillable = [
        'month',
        'year',
        'registration_fee',
        'course_fee',
        'total_fee',
        'royalty',
        'student_active',
        'student_new',
        'student_out',
        'student_leave',
        'status',
        'branch_id',
        'user_id',
        'approver_id',
    ];

    protected $casts = [
        'registration_fee' => 'double',
        'course_fee' => 'double',
        'total_fee' => 'double',
        'royalty' => 'double',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
