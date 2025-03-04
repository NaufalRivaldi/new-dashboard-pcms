<?php

namespace App\Livewire\Reports;

use App\Models\Branch;
use App\Services\AnalysisService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Livewire\Component;

class TopFiveActiveStudentEducation extends Component
{
    use InteractsWithPageFilters;

    private function getRecords(): array
    {
        return app(AnalysisService::class)->getTopFiveActiveStudentEducationRecords();
    }

    public function render()
    {
        return view('livewire.reports.top-five-active-student-education', [
            'records' => $this->getRecords()['results'],
            'educations' => $this->getRecords()['educations'],
        ]);
    }

    public function getBranchName(?int $branchId = null): string
    {
        if (!is_null($branchId)) {
            return Branch::find($branchId)->name ?? '-';
        }

        return '-';
    }

    public function getTotalValue(array $data, int $educationId): float
    {
        return number_format(collect($data)->firstWhere('education_id', $educationId)['total'] ?? 0);
    }
}
