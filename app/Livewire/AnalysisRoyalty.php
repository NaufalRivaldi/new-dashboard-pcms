<?php

namespace App\Livewire;

use App\Services\AnalysisService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Collection;
use Livewire\Component;

class AnalysisRoyalty extends Component
{
    use InteractsWithPageFilters;

    private function getRecords(): Collection
    {
        return app(AnalysisService::class)->getRoyaltyRecords();
    }

    public function render()
    {
        return view('livewire.analysis-royalty', [
            'records' => $this->getRecords(),
        ]);
    }
}
