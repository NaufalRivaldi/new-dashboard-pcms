<?php

namespace App\Livewire;

use App\Services\AnalysisService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Collection;
use Livewire\Component;

class AnalysisFee extends Component
{
    use InteractsWithPageFilters;

    private function getRecords(): Collection
    {
        return app(AnalysisService::class)->getFeeRecords();
    }

    public function render()
    {
        return view('livewire.analysis-fee', [
            'records' => $this->getRecords(),
        ]);
    }
}
