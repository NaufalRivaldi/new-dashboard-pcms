<?php

namespace App\Livewire\Chart;

use App\Services\AnalysisService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class CompareActiveStudentLessonChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = null;

    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        $records = app(AnalysisService::class)->getCompareActiveStudentLessonRecords();
        $data = $records['results'];
        $isMonthly = $records['isMonthly'];

        $datasets = [];

        foreach ($records['lessons'] as $lesson) {
            $datasets[] = [
                'label' => $lesson->name,
                'data' => collect($data)
                    ->map(function ($result) use ($lesson) {
                        return [
                            collect($result[0]['details'])
                                ->firstWhere('lesson_id', $lesson->id)
                                ['total']
                                ?? 0,
                            collect($result[1]['details'])
                                ->firstWhere('lesson_id', $lesson->id)
                                ['total']
                                ?? 0
                        ];
                    })
                    ->flatten()
                    ->all(),
                'backgroundColor' => generateRandomHexColor(),
                'borderWidth' => 0,
            ];
        }

        $labels = collect($records['results'])
            ->flatten(1)
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
        return 'bar';
    }
}
