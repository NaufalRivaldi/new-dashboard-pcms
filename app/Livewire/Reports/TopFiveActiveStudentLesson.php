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
        $records = $this->getRecords();

        return view('livewire.reports.top-five-active-student-lesson', [
            'records' => $records['results'],
            'lessons' => $records['lessons'],
        ]);
    }

    public function getBranchName(?int $branchId = null): string
    {
        if (!is_null($branchId)) {
            return Branch::find($branchId)->name ?? '-';
        }

        return '-';
    }

    public function getTotalValue(Collection $data, int $lessonId): string
    {
        return number_format($data->firstWhere('lesson_id', $lessonId)['total'] ?? 0);
    }
}
