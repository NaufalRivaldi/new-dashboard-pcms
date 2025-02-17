<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\ImportedActiveStudent;
use App\Models\ImportedActiveStudentDetail;
use App\Models\Lesson;
use Illuminate\Support\Collection;

class ActiveStudentLessonImport extends BaseImport
{
    protected $model = ImportedActiveStudent::class;

    protected function process(
        Collection $collection,
        Branch $branch,
        array $customData,
    ): void {
        $importedActiveStudentData = [
            'month' => $customData['month'],
            'year' => $customData['year'],
            'total' => $collection->sum($customData['month']+1) ?? 0,
            'branch_id' => $branch->id,
            'user_id' => auth()->user()->id,
        ];

        $importedActiveStudent = ImportedActiveStudent::create($importedActiveStudentData);

        $importedActiveStudentDetailData = $collection
            ->map(function ($data) use ($importedActiveStudent, $customData) {
                $lesson = Lesson::firstOrCreate([
                    'name' => $data[1],
                ]);

                return [
                    'total' => $data[$customData['month']+1] ?? 0,
                    'lesson_id' => $lesson->id,
                    'imported_active_student_id' => $importedActiveStudent->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->all();

        ImportedActiveStudentDetail::insert($importedActiveStudentDetailData);
    }
}
