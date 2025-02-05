<?php

namespace App\Services;

use App\Models\ImportedFee;

class ImportService
{
    public function isImportedFeeIsExists(
        ?int $month = null,
        ?int $year = null,
        ?int $branchId = null,
    ): bool {
        return ImportedFee::where('month', $month)
            ->where('year', $year)
            ->where('branch_id', $branchId)
            ->exists();
    }
}