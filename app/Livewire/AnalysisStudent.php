<?php

namespace App\Livewire;

use App\Services\AnalysisService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Collection;
use Livewire\Component;

class AnalysisStudent extends Component
{
    use InteractsWithPageFilters;

    private function getRecords(): Collection
    {
        return app(AnalysisService::class)->getStudentRecords();
    }

    public function render()
    {
        return view('livewire.analysis-student', [
            'records' => $this->getRecords(),
        ]);
    }
}
