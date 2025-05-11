<?php

namespace App\Livewire;

use App\Services\AnalysisService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Livewire\Component;

class AnalysisStudent extends Component
{
    use InteractsWithPageFilters;

    private function getRecords(): array
    {
        return app(AnalysisService::class)->getStudentRecords();
    }

    public function render()
    {
        $records = $this->getRecords();

        return view('livewire.analysis-student', [
            'records' => $records['records'],
            'isMonthly' => $records['isMonthly'],
        ]);
    }
}
