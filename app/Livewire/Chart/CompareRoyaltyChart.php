<?php

namespace App\Livewire\Chart;

use App\Services\AnalysisService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class CompareRoyaltyChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = null;

    protected static ?string $pollingInterval = '10s';

    protected function getData(): array
    {
        $data = app(AnalysisService::class)->getCompareRoyaltyRecords();

        $datasets = [
            [
                'label' => __('Total Royalty'),
                'data' => $data
                    ->flatten(1)
                    ->pluck('total_royalty')
                    ->transform(function ($value) {
                        return (float) $value;
                    })
                    ->all(),
                'backgroundColor' => '#f39c12',
                'type' => 'line',
                'fill' => false,
                'tension' => 0,
            ],
        ];

        $labels = $data
            ->flatten(1)
            ->map(function ($summary) {
                return [
                    periodWithBranchFormatter($summary),
                ];
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
