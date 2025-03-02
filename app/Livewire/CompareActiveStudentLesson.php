<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Services\AnalysisService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Arr;
use Livewire\Component;

class CompareActiveStudentLesson extends Component
{
    use InteractsWithPageFilters;

    private function getRecords(): array
    {
        return app(AnalysisService::class)->getCompareActiveStudentLessonRecords();
    }

    public function render()
    {
        return view('livewire.compare-active-student-lesson', [
            'records' => $this->getRecords()['results'],
            'lessons' => $this->getRecords()['lessons'],
            'firstBranchName' => $this->getBranchName(
                Arr::get(app(AnalysisService::class)->getCompareFilters(), 'firstBranchId')
            ),
            'secondBranchName' => $this->getBranchName(
                Arr::get(app(AnalysisService::class)->getCompareFilters(), 'secondBranchId')
            ),
        ]);
    }

    public function getBranchName(?int $branchId = null): string
    {
        if (!is_null($branchId)) {
            return Branch::find($branchId)->name ?? '-';
        }

        return '-';
    }

    public function getTotalValue(array $data, int $lessonId): float
    {
        return number_format(collect($data)->firstWhere('lesson_id', $lessonId)['total'] ?? 0);
    }
}
