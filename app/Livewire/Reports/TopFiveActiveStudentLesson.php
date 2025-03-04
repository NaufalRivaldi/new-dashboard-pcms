<?php

namespace App\Livewire\Reports;

use App\Models\Branch;
use App\Services\AnalysisService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Collection;
use Livewire\Component;

class TopFiveActiveStudentLesson extends Component
{
    use InteractsWithPageFilters;

    private function getRecords(): array
    {
        return app(AnalysisService::class)->getTopFiveActiveStudentLessonRecords();
    }

    public function render()
    {
        return view('livewire.reports.top-five-active-student-lesson', [
            'records' => $this->getRecords()['results'],
            'lessons' => $this->getRecords()['lessons'],
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
