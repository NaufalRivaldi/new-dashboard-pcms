<?php

namespace App\Livewire\Chart;

use App\Services\AnalysisService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class AnalysisActiveStudentEducation extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = null;

    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        $records = app(AnalysisService::class)->getActiveStudentEducationRecords();
        $data = $records['results'];
        $isMonthly = $records['isMonthly'];

        $datasets = [];

        foreach ($records['educations'] as $education) {
            $datasets[] = [
                'label' => $education->name,
                'data' => collect($data)
                    ->map(function ($result) use ($education) {
                        return [
                            collect($result['details'])
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

        $labels = $data
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
