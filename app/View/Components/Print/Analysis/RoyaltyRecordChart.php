<?php

namespace App\View\Components\Print\Analysis;

class RoyaltyRecordChart extends FeeRecordChart
{
    protected $viewName = 'components.print.analysis.royalty-record-chart';

    protected function getData(): array
    {
        $data = $this->records;
        $isMonthly = $this->isMonthly;

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
}
