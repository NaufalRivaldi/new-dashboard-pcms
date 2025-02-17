<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\ImportedNewStudent;
use Illuminate\Support\Collection;

class NewStudentImport extends BaseImport
{
    protected $model = ImportedNewStudent::class;

    protected function process(
        Collection $collection,
        Branch $branch,
        array $customData,
    ): void {
        $importedNewStudentData = [
            'month' => $customData['month'],
            'year' => $customData['year'],
            'total' => $collection->count(),
            'branch_id' => $branch->id,
            'user_id' => auth()->user()->id,
        ];

        ImportedNewStudent::create($importedNewStudentData);
    }
}
