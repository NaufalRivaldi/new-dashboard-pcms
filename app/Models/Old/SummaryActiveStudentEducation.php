<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SummaryActiveStudentEducation extends Model
{
    protected $connection = 'old_prod';

    protected $table = 'summary_sa_pendidikan';

    public function pendidikan(): BelongsTo
    {
        return $this->belongsTo(Pendidikan::class, 'pendidikan_id', 'id');
    }
}
