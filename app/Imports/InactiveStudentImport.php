<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\ImportedInactiveStudent;
use Illuminate\Support\Collection;

class InactiveStudentImport extends BaseImport
{
    protected $model = ImportedInactiveStudent::class;

    protected function process(
        Collection $collection,
        Branch $branch,
        array $customData,
    ): void {
        $importedInactiveStudentData = [
            'month' => $customData['month'],
            'year' => $customData['year'],
            'total' => $collection->count(),
            'branch_id' => $branch->id,
            'user_id' => auth()->user()->id,
        ];

        ImportedInactiveStudent::create($importedInactiveStudentData);
    }
}
