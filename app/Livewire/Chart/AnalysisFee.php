<?php

namespace App\Livewire\Chart;

use App\Enums\Month;
use App\Services\AnalysisService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class AnalysisFee extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = null;

    protected static ?string $pollingInterval = '10s';

    protected function getData(): array
    {
        $data = app(AnalysisService::class)->getFeeRecords();

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
                return [
                    periodFormatter($summary),
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
        return 'bar';
    }
}
