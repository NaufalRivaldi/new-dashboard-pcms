<?php

namespace App\Livewire\Chart;

use App\Services\AnalysisService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class AnalysisRoyalty extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = null;

    protected static ?string $pollingInterval = '10s';

    protected function getData(): array
    {
        $records = app(AnalysisService::class)->getRoyaltyRecords();
        $data = $records['records'];
        $isMonthly = $records['isMonthly'];

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

    protected function getType(): string
    {
        return 'line';
    }
}
