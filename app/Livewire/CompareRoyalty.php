<?php

namespace App\Livewire;

use App\Services\AnalysisService;
use App\Traits\HasReportFilter;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Arr;
use Livewire\Component;

class CompareRoyalty extends Component
{
    use InteractsWithPageFilters, HasReportFilter;

    private function getRecords(): array
    {
        return app(AnalysisService::class)->getCompareRoyaltyRecords();
    }

    public function render()
    {
        $filters = app(AnalysisService::class)->getCompareFilters();
        $records = $this->getRecords();

        return view('livewire.compare-royalty', [
            'records' => $records['records'],
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
}
