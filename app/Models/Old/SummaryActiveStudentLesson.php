<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SummaryActiveStudentLesson extends Model
{
    protected $connection = 'old_prod';

    protected $table = 'summary_sa_materi';

    public function materi(): BelongsTo
    {
        return $this->belongsTo(Materi::class, 'materi_id', 'id');
    }
}
