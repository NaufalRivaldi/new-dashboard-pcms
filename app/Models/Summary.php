<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Summary extends Model
{
    protected $fillable = [
        'month',
        'year',
        'registration_fee',
        'course_fee',
        'total_fee',
        'royalty',
        'active_student',
        'new_student',
        'inactive_student',
        'leave_student',
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

    public function summaryActiveStudentEducation(): HasMany
    {
        return $this->hasMany(SummaryActiveStudentEducation::class);
    }

    public function summaryActiveStudentLesson(): HasMany
    {
        return $this->hasMany(SummaryActiveStudentLesson::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function scopePending(Builder $query)
    {
        $query->where('status', false);
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status == true;
    }
}
