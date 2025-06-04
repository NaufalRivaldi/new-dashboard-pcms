<?php

namespace App\View\Components\Print\Compare;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class FeeRecordChart extends Component
{
    protected $viewName = 'components.print.compare.fee-record-chart';

    public function __construct(
        protected Collection $records,
        protected bool $isMonthly = true,
    ) {}

    protected function getData(): array
    {
        $data = $this->records;
        $isMonthly = $this->isMonthly;

        $datasets = [
            [
                'label' => __('Total fee'),
                'data' => $data
                    ->flatten(1)
                    ->pluck('total_total_fee')
                    ->transform(function ($value) {
                        return (float) $value;
                    })
                    ->all(),
                'backgroundColor' => '#f39c12',
                'type' => 'line',
                'fill' => false,
                'tension' => 0,
            ],
            [
                'label' => __('Registration fee'),
                'data' => $data
                    ->flatten(1)
                    ->pluck('total_registration_fee')
                    ->transform(function ($value) {
                        return (float) $value;
                    })
                    ->all(),
                'backgroundColor' => '#3498db',
                'borderWidth' => 0,
            ],
            [
                'label' => __('Course fee'),
                'data' => $data
                    ->flatten(1)
                    ->pluck('total_course_fee')
                    ->transform(function ($value) {
                        return (float) $value;
                    })
                    ->all(),
                'backgroundColor' => '#1abc9c',
                'borderWidth' => 0,
            ],
        ];

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

    public function render(): View|Closure|string
    {
        return view($this->viewName, [
            'data' => $this->getData(),
        ]);
    }
}
