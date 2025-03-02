<?php

namespace App\Livewire;

use App\Services\AnalysisService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Livewire\Component;

class AnalysisActiveStudentEducation extends Component
{
    use InteractsWithPageFilters;

    private function getRecords(): array
    {
        return app(AnalysisService::class)->getActiveStudentEducationRecords();
    }

    public function render()
    {
        return view('livewire.analysis-active-student-education', [
            'records' => $this->getRecords()['results'],
            'educations' => $this->getRecords()['educations'],
        ]);
    }
}
