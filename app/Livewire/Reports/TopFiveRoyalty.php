<?php

namespace App\Livewire\Reports;

use App\Models\Branch;
use App\Services\AnalysisService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Collection;
use Livewire\Component;

class TopFiveRoyalty extends Component
{
    use InteractsWithPageFilters;

    private function getRecords(): array
    {
        return app(AnalysisService::class)->getTopFiveRoyaltyRecords();
    }

    public function render()
    {
        return view('livewire.reports.top-five-royalty', [
            'records' => $this->getRecords()['records'],
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
