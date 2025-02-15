<?php

namespace App\Services;

use App\Enums\PaymentType;
use App\Models\ImportedActiveStudent;
use App\Models\ImportedActiveStudentEducation;
use App\Models\ImportedFee;
use App\Models\ImportedInactiveStudent;
use App\Models\ImportedLeaveStudent;
use App\Models\ImportedNewStudent;
use App\Models\Summary;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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

    public function isSummaryExists(
        ?int $month = null,
        ?int $year = null,
        ?int $branchId = null,
    ): bool {
        return Summary::where('month', $month)
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
            'leave_student' => $importedActiveStudent->total,
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
}