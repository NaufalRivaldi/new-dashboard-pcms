<?php

namespace App\Livewire\Chart;

use App\Services\AnalysisService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class CompareActiveStudentEducationChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = null;

    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        $data = app(AnalysisService::class)->getCompareActiveStudentEducationRecords();

        $datasets = [];

        foreach ($data['educations'] as $education) {
            $datasets[] = [
                'label' => $education->name,
                'data' => collect($data['results'])
                    ->map(function ($result) use ($education) {
                        return [
                            collect($result[0]['details'])
                                ->firstWhere('education_id', $education->id)
                                ['total']
                                ?? 0,
                            collect($result[1]['details'])
                                ->firstWhere('education_id', $education->id)
                                ['total']
                                ?? 0
                        ];
                    })
                    ->flatten()
                    ->all(),
                'backgroundColor' => $education->color ?? generateRandomHexColor(),
                'borderWidth' => 0,
            ];
        }

        $labels = collect($data['results'])
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
