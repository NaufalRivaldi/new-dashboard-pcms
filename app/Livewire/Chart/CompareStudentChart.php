<?php

namespace App\Livewire\Chart;

use App\Services\AnalysisService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class CompareStudentChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = null;

    protected static ?string $pollingInterval = '10s';

    protected function getData(): array
    {
        $data = app(AnalysisService::class)->getCompareStudentRecords();

        $datasets = [
            [
                'label' => __('Active student'),
                'data' => $data
                    ->flatten(1)
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
                    ->flatten(1)
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
                    ->flatten(1)
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
                    ->flatten(1)
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
        return 'bar';
    }
}
