<?php

namespace App\View\Components\Print\TopOrUnder;

use App\Models\Branch;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class StudentEducationRecordChart extends Component
{
    public function __construct(
        protected Collection $records,
        protected Collection $educations,
    ) {}

    protected function getData(): array
    {
        $data = $this->records;

        $datasets = [];

        foreach ($this->educations as $education) {
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
                'backgroundColor' => generateRandomHexColor(),
                'borderWidth' => 0,
            ];
        }

        $labels = $data
            ->map(function ($summary) {
                return Branch::find($summary['branch_id'])->name ?? '-';
            })
            ->flatten()
            ->all();

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.print.top-or-under.student-education-record-chart', [
            'data' => $this->getData(),
        ]);
    }
}
