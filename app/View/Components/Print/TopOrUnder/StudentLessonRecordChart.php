<?php

namespace App\View\Components\Print\TopOrUnder;

use App\Models\Branch;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class StudentLessonRecordChart extends Component
{
    public function __construct(
        protected Collection $records,
        protected Collection $lessons,
    ) {}

    protected function getData(): array
    {
        $data = $this->records;

        $datasets = [];

        foreach ($this->lessons as $lesson) {
            $datasets[] = [
                'label' => $lesson->name,
                'data' => collect($data)
                    ->map(function ($result) use ($lesson) {
                        return [
                            collect($result['details'])
                                ->firstWhere('lesson_id', $lesson->id)
                                ['total']
                                ?? 0
                        ];
                    })
                    ->flatten()
                    ->all(),
                'backgroundColor' => generateRandomHexColor(),
                'borderWidth' => 0,
            ];
        }

        $labels = $data
            ->map(function ($summary) {
                return Branch::find($summary['branch_id'])->name ?? '-';
            })
            ->flatten()
            ->all();

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.print.top-or-under.student-lesson-record-chart', [
            'data' => $this->getData(),
        ]);
    }
}
