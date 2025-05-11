<?php

namespace App\Livewire\Chart;

use App\Models\Branch;
use App\Services\AnalysisService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class TopFiveRoyaltyChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = null;

    protected static ?string $pollingInterval = '10s';

    protected function getData(): array
    {
        $data = app(AnalysisService::class)->getTopFiveRoyaltyRecords()['records'];

        $datasets = [
            [
                'label' => __('Royalty'),
                'data' => $data
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
            ->map(function ($summary) {
                return [
                    Branch::find($summary['branch_id'])->name ?? '-'
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
