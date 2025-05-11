<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\Education;
use App\Models\ImportedActiveStudentEducation;
use App\Models\ImportedActiveStudentEducationDetail;
use Exception;
use Illuminate\Support\Collection;

class ActiveStudentEducationImport extends BaseImport
{
    protected $model = ImportedActiveStudentEducation::class;

    protected function process(
        Collection $collection,
        Branch $branch,
        array $customData,
    ): void {
        $importedActiveStudentEducationData = [
            'month' => $customData['month'],
            'year' => $customData['year'],
            'total' => $collection->sum($customData['month']+1) ?? 0,
            'branch_id' => $branch->id,
            'user_id' => auth()->user()->id,
        ];

        $importedActiveStudentEducation = ImportedActiveStudentEducation::create($importedActiveStudentEducationData);

        $importedActiveStudentEducationDetailData = $collection
            ->map(function ($data) use ($importedActiveStudentEducation, $customData) {
                $education = Education::firstWhere('name', $data[1]);

                if (!$education) {
                    throw new Exception(
                        __('The :value in :resource is not available, please adjust it to match existing data or contact the admin.', [
                            'value' => $data[1],
                            'resource' => __('Education'),
                        ]),
                        422
                    );
                }

                return [
                    'total' => $data[$customData['month']+1] ?? 0,
                    'education_id' => $education->id,
                    'imported_active_student_education_id' => $importedActiveStudentEducation->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->all();

        ImportedActiveStudentEducationDetail::insert($importedActiveStudentEducationDetailData);
    }
}
