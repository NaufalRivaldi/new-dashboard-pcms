<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Summary extends Model
{
    protected $connection = 'old_prod';

    protected $table = 'summary';

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_approve_id', 'id');
    }

    public function summaryASL(): HasMany
    {
        return $this->hasMany(SummaryActiveStudentLesson::class, 'summary_id', 'id');
    }

    public function summaryASE(): HasMany
    {
        return $this->hasMany(SummaryActiveStudentEducation::class, 'summary_id', 'id');
    }
}
