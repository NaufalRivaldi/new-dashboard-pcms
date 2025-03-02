<?php

namespace App\Livewire\Chart;

use App\Enums\Month;
use App\Services\AnalysisService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class AnalysisActiveStudentLesson extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = null;

    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        $data = app(AnalysisService::class)->getActiveStudentLessonRecords();

        $datasets = [];

        foreach ($data['lessons'] as $lesson) {
            $datasets[] = [
                'label' => $lesson->name,
                'data' => collect($data['results'])
                    ->map(function ($result) use ($lesson) {
                        return [
                            collect($result['details'])
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

        $labels = collect($data['results'])
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
