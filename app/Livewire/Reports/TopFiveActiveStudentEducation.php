<?php

namespace App\Livewire\Reports;

use App\Models\Branch;
use App\Services\AnalysisService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Collection;
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
        $records = $this->getRecords();

        return view('livewire.reports.top-five-active-student-education', [
            'records' => $records['results'],
            'educations' => $records['educations'],
        ]);
    }

    public function getBranchName(?int $branchId = null): string
    {
        if (!is_null($branchId)) {
            return Branch::find($branchId)->name ?? '-';
        }

        return '-';
    }

    public function getTotalValue(Collection $data, int $educationId): string
    {
        return number_format($data->firstWhere('education_id', $educationId)['total'] ?? 0);
    }
}
