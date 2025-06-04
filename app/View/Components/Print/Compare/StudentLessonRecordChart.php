<?php

namespace App\View\Components\Print\Compare;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class StudentLessonRecordChart extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        protected Collection $records,
        protected Collection $lessons,
        protected bool $isMonthly = true,
    ) {}

    protected function getData(): array
    {
        $data = $this->records;
        $isMonthly = $this->isMonthly;

        $datasets = [];

        foreach ($this->lessons as $lesson) {
            $datasets[] = [
                'label' => $lesson->name,
                'data' => collect($data)
                    ->map(function ($result) use ($lesson) {
                        return [
                            collect($result[0]['details'])
                                ->firstWhere('lesson_id', $lesson->id)
                                ['total']
                                ?? 0,
                            collect($result[1]['details'])
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
            ->flatten(1)
            ->map(function ($summary) use ($isMonthly) {
                $label = null;

                if ($isMonthly) {
                    $label = periodFormatter($summary);
                } else {
                    $label = $summary['year'];
                }

                return [ $label ];
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
        return view('components.print.compare.student-lesson-record-chart', [
            'data' => $this->getData(),
        ]);
    }
}
