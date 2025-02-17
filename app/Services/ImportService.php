<?php

namespace App\Services;

use App\Enums\Month;
use App\Enums\PaymentType;
use App\Models\ImportedActiveStudent;
use App\Models\ImportedActiveStudentEducation;
use App\Models\ImportedFee;
use App\Models\ImportedInactiveStudent;
use App\Models\ImportedLeaveStudent;
use App\Models\ImportedNewStudent;
use EightyNine\ExcelImport\ExcelImportAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ImportService
{
    public function isImportedDataExists(
        string $modelName,
        ?int $month = null,
        ?int $year = null,
        ?int $branchId = null,
    ): bool {
        return $modelName::where('month', $month)
            ->where('year', $year)
            ->where('branch_id', $branchId)
            ->exists();
    }

    public function sendNotificationDataExists(): void
    {
        NotificationService::danger(
            title: __('This imported data already exists!'),
            body: __('Please use a different branch, month, or year.'),
        );
    }

    public function calculateTotalAndRoyaltyFee(
        float $registrationFee,
        float $courseFee,
    ): array {
        $total = $registrationFee + $courseFee;
        $royalty = $total * (10/100);

        return [
            'total' => $total,
            'royalty' => $royalty,
        ];
    }

    public function generateSummaryData(
        int|string $branchId,
        int|string $month,
        int|string $year,
    ): array {
        $importedFee = $this->importedDataBuilder(
                new ImportedFee(),
                $branchId,
                $month,
                $year,
            )
            ->first();

        if (is_null($importedFee)) {
            return $this->emptyImportedDataResult('LA03');
        }

        $importedActiveStudent = $this->importedDataBuilder(
                new ImportedActiveStudent(),
                $branchId,
                $month,
                $year,
            )
            ->with('details.lesson')
            ->first();

        if (is_null($importedActiveStudent)) {
            return $this->emptyImportedDataResult('LA06');
        }

        $importedActiveStudentEducation = $this->importedDataBuilder(
                new ImportedActiveStudentEducation(),
                $branchId,
                $month,
                $year,
            )
            ->with('details.education')
            ->first();

        if (is_null($importedActiveStudentEducation)) {
            return $this->emptyImportedDataResult('LA07');
        }

        $importedNewStudent = $this->importedDataBuilder(
                new ImportedNewStudent(),
                $branchId,
                $month,
                $year,
            )
            ->first();

        if (is_null($importedNewStudent)) {
            return $this->emptyImportedDataResult('LA09');
        }

        $importedInactiveStudent = $this->importedDataBuilder(
                new ImportedInactiveStudent(),
                $branchId,
                $month,
                $year,
            )
            ->first();

        if (is_null($importedInactiveStudent)) {
            return $this->emptyImportedDataResult('LA12');
        }

        $importedLeaveStudent = $this->importedDataBuilder(
                new ImportedLeaveStudent(),
                $branchId,
                $month,
                $year,
            )
            ->first();

        if (is_null($importedLeaveStudent)) {
            return $this->emptyImportedDataResult('LA13');
        }

        $fees = $this->calculateFees($importedFee);
        $calculationResult = $this->calculateTotalAndRoyaltyFee(
            $fees['registration'],
            $fees['course'],
        );

        return [
            'status' => true,
            'registration_fee' => $fees['registration'],
            'course_fee' => $fees['course'],
            'total_fee' => $calculationResult['total'],
            'royalty' => $calculationResult['royalty'],
            'active_student' => $importedActiveStudent->total,
            'new_student' => $importedNewStudent->total,
            'inactive_student' => $importedInactiveStudent->total,
            'leave_student' => $importedLeaveStudent->total,
            'summary_active_student_education' => $importedActiveStudentEducation
                ->details
                ->map(function ($details) {
                    return [
                        'education_id' => $details['education_id'],
                        'total' => $details['total'],
                    ];
                })
                ->all(),
            'summary_active_student_lesson' => $importedActiveStudent
                ->details
                ->map(function ($details) {
                    return [
                        'lesson_id' => $details['lesson_id'],
                        'total' => $details['total'],
                    ];
                })
                ->all(),
        ];
    }

    private function importedDataBuilder(
        Model $model,
        int|string $branchId,
        int|string $month,
        int|string $year,
    ): Builder {
        return $model::where('branch_id', $branchId)
            ->where('month', $month)
            ->where('year', $year);
    }

    private function emptyImportedDataResult(string $resourceName): array
    {
        return [
            'status' => false,
            'message' => __('The :resource is empty. You need to add or import the data!', [
                'resource' => $resourceName,
            ])
        ];
    }

    private function calculateFees(ImportedFee $importedFee): array
    {
        $registrationFee = 0;
        $courseFee = 0;

        if ($importedFee->details) {
            $registrationFee = $importedFee->details
                ->where('type', PaymentType::Register->value)
                ->sum('nominal');

            $courseFee = $importedFee->details
                ->where('type', PaymentType::Course->value)
                ->sum('nominal');
        }

        return [
            'registration' => $registrationFee,
            'course' => $courseFee,
        ];
    }

    public function importAction(string $importModel)
    {
        return ExcelImportAction::make()
            ->color("primary")
            ->processCollectionUsing(function (string $modelClass, Collection $collection) {
                $collection
                    ->map(function ($data) {
                        return $data->values();
                    });

                return $collection;
            })
            ->use($importModel)
            ->beforeUploadField([
                app(FormService::class)->branchSelectOption(),
                Select::make('month')
                    ->options(Month::class)
                    ->searchable()
                    ->required(),
                TextInput::make('year')
                    ->required()
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(2030),
            ])
            ->beforeImport(function (array $data, $livewire, $excelImportAction) {
                $customData = [
                    'branch_id' => (int) $data['branch_id'],
                    'month' => (int) $data['month'],
                    'year' => (int) $data['year'],
                ];

                $excelImportAction->customImportData($customData);
            });
    }
}