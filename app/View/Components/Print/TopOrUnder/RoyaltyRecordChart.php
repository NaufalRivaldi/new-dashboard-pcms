<?php

namespace App\View\Components\Print\TopOrUnder;

use App\Models\Branch;

class RoyaltyRecordChart extends FeeRecordChart
{
    protected $viewName = 'components.print.top-or-under.royalty-record-chart';

    protected function getData(): array
    {
        $data = $this->records;

        $datasets = [
            [
                'label' => __('Total Royalty'),
                'data' => $data
                    ->pluck('total_royalty')
                    ->transform(function ($value) {
                        return (float) $value;
                    })
                    ->all(),
                'type' => 'line',
                'backgroundColor' => '#f39c12',
                'borderColor' => '#f39c12',
                'fill' => false,
                'pointHoverRadius' => 10,
                'pointRadius' => 5,
                'pointStyle' => 'circle',
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
