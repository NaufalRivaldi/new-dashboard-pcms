<?php

namespace App\Livewire;

use App\Services\AnalysisService;
use App\Traits\HasReportFilter;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Arr;
use Livewire\Component;

class CompareActiveStudentEducation extends Component
{
    use InteractsWithPageFilters, HasReportFilter;

    private function getRecords(): array
    {
        return app(AnalysisService::class)->getCompareActiveStudentEducationRecords();
    }

    public function render()
    {
        $filters = app(AnalysisService::class)->getCompareFilters();
        $records = $this->getRecords();

        return view('livewire.compare-active-student-education', [
            'records' => $records['results'],
            'educations' => $records['educations'],
            'isMonthly' => $records['isMonthly'],
            'firstBranchName' => $this->getBranchName(
                Arr::get($filters, 'firstBranchId')
            ),
            'secondBranchName' => $this->getBranchName(
                Arr::get($filters, 'secondBranchId')
            ),
            'firstRegionName' => $this->getRegionName(
                Arr::get($filters, 'firstRegionId')
            ),
            'secondRegionName' => $this->getRegionName(
                Arr::get($filters, 'secondRegionId')
            ),
        ]);
    }

    public function getTotalValue(array $data, int $educationId): string
    {
        return number_format(collect($data)->firstWhere('education_id', $educationId)['total'] ?? 0);
    }
}
