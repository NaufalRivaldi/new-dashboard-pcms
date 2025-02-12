<?php

namespace App\Services;

use App\Models\ImportedActiveStudent;
use App\Models\ImportedActiveStudentEducation;
use App\Models\ImportedFee;
use App\Models\ImportedInactiveStudent;
use App\Models\ImportedLeaveStudent;
use App\Models\ImportedNewStudent;

class ImportService
{
    public function isImportedFeeExists(
        ?int $month = null,
        ?int $year = null,
        ?int $branchId = null,
    ): bool {
        return ImportedFee::where('month', $month)
            ->where('year', $year)
            ->where('branch_id', $branchId)
            ->exists();
    }

    public function isImportedActiveStudentExists(
        ?int $month = null,
        ?int $year = null,
        ?int $branchId = null,
    ): bool {
        return ImportedActiveStudent::where('month', $month)
            ->where('year', $year)
            ->where('branch_id', $branchId)
            ->exists();
    }

    public function isImportedActiveStudentEducationExists(
        ?int $month = null,
        ?int $year = null,
        ?int $branchId = null,
    ): bool {
        return ImportedActiveStudentEducation::where('month', $month)
            ->where('year', $year)
            ->where('branch_id', $branchId)
            ->exists();
    }

    public function isImportedNewStudentExists(
        ?int $month = null,
        ?int $year = null,
        ?int $branchId = null,
    ): bool {
        return ImportedNewStudent::where('month', $month)
            ->where('year', $year)
            ->where('branch_id', $branchId)
            ->exists();
    }

    public function isImportedInactiveStudentExists(
        ?int $month = null,
        ?int $year = null,
        ?int $branchId = null,
    ): bool {
        return ImportedInactiveStudent::where('month', $month)
            ->where('year', $year)
            ->where('branch_id', $branchId)
            ->exists();
    }

    public function isImportedLeaveStudentExists(
        ?int $month = null,
        ?int $year = null,
        ?int $branchId = null,
    ): bool {
        return ImportedLeaveStudent::where('month', $month)
            ->where('year', $year)
            ->where('branch_id', $branchId)
            ->exists();
    }
}