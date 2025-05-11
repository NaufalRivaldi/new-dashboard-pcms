<?php

namespace App\Livewire;

use App\Services\AnalysisService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Livewire\Component;

class AnalysisFee extends Component
{
    use InteractsWithPageFilters;

    private function getRecords(): array
    {
        return app(AnalysisService::class)->getFeeRecords();
    }

    public function render()
    {
        $records = $this->getRecords();

        return view('livewire.analysis-fee', [
            'records' => $records['records'],
            'isMonthly' => $records['isMonthly'],
        ]);
    }
}
