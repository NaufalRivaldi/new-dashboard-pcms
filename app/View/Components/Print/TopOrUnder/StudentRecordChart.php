<?php

namespace App\View\Components\Print\TopOrUnder;

use App\Models\Branch;

class StudentRecordChart extends FeeRecordChart
{
    protected $viewName = 'components.print.top-or-under.student-record-chart';

    protected function getData(): array
    {
        $data = $this->records;

        $datasets = [
            [
                'label' => __('Active student'),
                'data' => $data
                    ->pluck('total_active_student')
                    ->transform(function ($value) {
                        return (float) $value;
                    })
                    ->all(),
                'backgroundColor' => '#3498db',
                'borderWidth' => 0,
            ],
            [
                'label' => __('New student'),
                'data' => $data
                    ->pluck('total_new_student')
                    ->transform(function ($value) {
                        return (float) $value;
                    })
                    ->all(),
                'backgroundColor' => '#27ae60',
                'borderWidth' => 0,
            ],
            [
                'label' => __('Inactive student'),
                'data' => $data
                    ->pluck('total_inactive_student')
                    ->transform(function ($value) {
                        return (float) $value;
                    })
                    ->all(),
                'backgroundColor' => '#c0392b',
                'borderWidth' => 0,
            ],
            [
                'label' => __('Leave student'),
                'data' => $data
                    ->pluck('total_leave_student')
                    ->transform(function ($value) {
                        return (float) $value;
                    })
                    ->all(),
                'backgroundColor' => '#95a5a6',
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
}
