<?php

namespace App\Livewire;

use App\Services\AnalysisService;
use App\Traits\HasReportFilter;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Arr;
use Livewire\Component;

class CompareActiveStudentLesson extends Component
{
    use InteractsWithPageFilters, HasReportFilter;

    private function getRecords(): array
    {
        return app(AnalysisService::class)->getCompareActiveStudentLessonRecords();
    }

    public function render()
    {
        $filters = app(AnalysisService::class)->getCompareFilters();
        $records = $this->getRecords();

        return view('livewire.compare-active-student-lesson', [
            'records' => $records['results'],
            'lessons' => $records['lessons'],
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

    public function getTotalValue(array $data, int $lessonId): string
    {
        return number_format(collect($data)->firstWhere('lesson_id', $lessonId)['total'] ?? 0);
    }
}
