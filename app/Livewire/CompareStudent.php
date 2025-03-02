<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Services\AnalysisService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Livewire\Component;

class CompareStudent extends Component
{
    use InteractsWithPageFilters;

    private function getRecords(): Collection
    {
        return app(AnalysisService::class)->getCompareStudentRecords();
    }

    public function render()
    {
        return view('livewire.compare-student', [
            'records' => $this->getRecords(),
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
}
