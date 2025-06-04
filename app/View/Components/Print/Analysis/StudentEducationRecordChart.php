<?php

namespace App\View\Components\Print\Analysis;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class StudentEducationRecordChart extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        protected Collection $records,
        protected Collection $educations,
        protected bool $isMonthly = true,
    ) {}

    protected function getData(): array
    {
        $data = $this->records;
        $isMonthly = $this->isMonthly;

        $datasets = [];

        foreach ($this->educations as $education) {
            $datasets[] = [
                'label' => $education->name,
                'data' => collect($data)
                    ->map(function ($result) use ($education) {
                        return [
                            collect($result['details'])
                                ->firstWhere('education_id', $education->id)
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
        return view('components.print.analysis.student-education-record-chart', [
            'data' => $this->getData(),
        ]);
    }
}
