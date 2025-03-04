<?php

namespace App\Livewire\Chart;

use App\Models\Branch;
use App\Services\AnalysisService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class TopFiveActiveStudentEducationChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = null;

    protected static ?string $pollingInterval = '10s';

    protected function getData(): array
    {
        $data = app(AnalysisService::class)->getTopFiveActiveStudentEducationRecords();

        $datasets = [];

        foreach ($data['educations'] as $education) {
            $datasets[] = [
                'label' => $education->name,
                'data' => collect($data['results'])
                    ->map(function ($result) use ($education) {
                        return [
                            collect($result['details'])
                                ->firstWhere('education_id', $education->id)
                                ['total']
                                ?? 0,
                        ];
                    })
                    ->flatten()
                    ->all(),
                'backgroundColor' => $education->color ?? generateRandomHexColor(),
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
