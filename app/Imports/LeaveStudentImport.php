<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\ImportedLeaveStudent;
use Illuminate\Support\Collection;

class LeaveStudentImport extends BaseImport
{
    protected $model = ImportedLeaveStudent::class;

    protected function process(
        Collection $collection,
        Branch $branch,
        array $customData,
    ): void {
        $importedLeaveStudentData = [
            'month' => $customData['month'],
            'year' => $customData['year'],
            'total' => $collection->count(),
            'branch_id' => $branch->id,
            'user_id' => auth()->user()->id,
        ];

        ImportedLeaveStudent::create($importedLeaveStudentData);
    }
}
