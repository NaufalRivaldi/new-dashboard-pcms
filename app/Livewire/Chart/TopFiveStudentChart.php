<?php

namespace App\Livewire\Chart;

use App\Models\Branch;
use App\Services\AnalysisService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class TopFiveStudentChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = null;

    protected static ?string $pollingInterval = '10s';

    protected function getData(): array
    {
        $data = app(AnalysisService::class)->getTopFiveStudentRecords();

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
        return 'bar';
    }
}
