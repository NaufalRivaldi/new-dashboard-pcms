<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Services\AnalysisService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Arr;
use Livewire\Component;

class CompareActiveStudentEducation extends Component
{
    use InteractsWithPageFilters;

    private function getRecords(): array
    {
        return app(AnalysisService::class)->getCompareActiveStudentEducationRecords();
    }

    public function render()
    {
        $records = $this->getRecords();

        return view('livewire.compare-active-student-education', [
            'records' => $records['results'],
            'educations' => $records['educations'],
            'firstBranchName' => $this->getBranchName(
                Arr::get(app(AnalysisService::class)->getCompareFilters(), 'firstBranchId')
            ),
            'secondBranchName' => $this->getBranchName(
                Arr::get(app(AnalysisService::class)->getCompareFilters(), 'secondBranchId')
            ),
        ]);
    }

    public function getBranchName(?int $branchId = null): string
    {
        if (!is_null($branchId)) {
            return Branch::find($branchId)->name ?? '-';
        }

        return '-';
    }

    public function getTotalValue(array $data, int $educationId): string
    {
        return number_format(collect($data)->firstWhere('education_id', $educationId)['total'] ?? 0);
    }
}
