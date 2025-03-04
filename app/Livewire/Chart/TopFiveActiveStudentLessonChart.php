<?php

namespace App\Livewire\Chart;

use App\Models\Branch;
use App\Services\AnalysisService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class TopFiveActiveStudentLessonChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = null;

    protected static ?string $pollingInterval = '10s';

    protected function getData(): array
    {
        $data = app(AnalysisService::class)->getTopFiveActiveStudentLessonRecords();

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
                                ?? 0,
                        ];
                    })
                    ->flatten()
                    ->all(),
                'backgroundColor' => generateRandomHexColor(),
                'borderWidth' => 0,
            ];
        }

        $labels = $data['results']
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
