<?php

namespace App\Livewire;

use App\Services\AnalysisService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Collection;
use Livewire\Component;

class AnalysisRoyalty extends Component
{
    use InteractsWithPageFilters;

    private function getRecords(): array
    {
        return app(AnalysisService::class)->getRoyaltyRecords();
    }

    public function render()
    {
        $records = $this->getRecords();

        return view('livewire.analysis-royalty', [
            'records' => $records['records'],
            'isMonthly' => $records['isMonthly'],
        ]);
    }
}
