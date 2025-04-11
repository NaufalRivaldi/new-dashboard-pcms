<?php

namespace App\Models\Old;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cabang extends Model
{
    protected $connection = 'old_prod';

    protected $table = 'cabang';

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id', 'id');
    }
}
