<?php

namespace App\Livewire;

use App\Services\AnalysisService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Collection;
use Livewire\Component;

class AnalysisActiveStudentLesson extends Component
{
    use InteractsWithPageFilters;

    private function getRecords(): array
    {
        return app(AnalysisService::class)->getActiveStudentLessonRecords();
    }

    public function render()
    {
        $records = $this->getRecords();

        return view('livewire.analysis-active-student-lesson', [
            'records' => $records['results'],
            'lessons' => $records['lessons'],
            'isMonthly' => $records['isMonthly'],
        ]);
    }
}
