<?php

namespace App\View\Components\Print\TopOrUnder;

use App\Models\Branch;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class FeeRecordChart extends Component
{
    protected $viewName = 'components.print.top-or-under.fee-record-chart';

    public function __construct(protected Collection $records)
    {}

    protected function getData(): array
    {
        $data = $this->records;

        $datasets = [
            [
                'label' => __('Total fee'),
                'data' => $data
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
        return view($this->viewName, [
            'data' => $this->getData(),
        ]);
    }
}
