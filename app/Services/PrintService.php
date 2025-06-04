<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class PrintService extends AnalysisService
{
    protected function getFilters(): array
    {
        $filterStartPeriod = request()->get('start_period');
        $filterEndPeriod = request()->get('end_period');
        $filterStartYear = request()->get('start_year');
        $filterEndYear = request()->get('end_year');
        $branchId = request()->get('branch_id');
        $regionId = request()->get('region_id');

        $isMonthly = (
                !empty($filterStartPeriod)
                || !empty($filterEndPeriod)
            )
            || (
                empty($filterStartYear)
                || empty($filterEndYear)
            );

        $endPeriod = $startPeriod = null;

        if ($isMonthly) {
            $endPeriod = !empty($filterEndPeriod)
                ? $filterEndPeriod
                : Carbon::now()->format('Y-m');
            $startPeriod = !empty($filterStartPeriod)
                ? $filterStartPeriod
                : Carbon::parse($endPeriod)->subMonth(11)->format('Y-m');
        } else {
            $endPeriod = !empty($filterEndYear)
                ? $filterEndYear
                : Carbon::now()->format('Y');
            $startPeriod = !empty($filterStartYear)
                ? $filterStartYear
                : Carbon::parse($endPeriod)->subYear()->format('Y');
        }

        $periodValues = $this->getPeriodValues(
            $startPeriod,
            $endPeriod,
            $isMonthly,
        );

        return [
            'periodValues' => $periodValues,
            'branchId' => $branchId,
            'regionId' => $regionId,
            'isMonthly' => $isMonthly,
        ];
    }

    public function getCompareFilters(): array
    {
        $filterStartPeriod = request()->get('start_period');
        $filterEndPeriod = request()->get('end_period');
        $filterStartYear = request()->get('start_year');
        $filterEndYear = request()->get('end_year');
        $firstBranchId = request()->get('first_branch_id');
        $secondBranchId = request()->get('second_branch_id');
        $firstRegionId = request()->get('first_region_id');
        $secondRegionId = request()->get('second_region_id');

        $isMonthly = (
                !empty($filterStartPeriod)
                || !empty($filterEndPeriod)
            )
            || (
                empty($filterStartYear)
                || empty($filterEndYear)
            );

        $endPeriod = $startPeriod = null;

        if ($isMonthly) {
            $endPeriod = !empty($filterEndPeriod)
                ? $filterEndPeriod
                : Carbon::now()->format('Y-m');
            $startPeriod = !empty($filterStartPeriod)
                ? $filterStartPeriod
                : Carbon::parse($endPeriod)->subMonth(11)->format('Y-m');
        } else {
            $endPeriod = !empty($filterEndYear)
                ? $filterEndYear
                : Carbon::now()->format('Y');
            $startPeriod = !empty($filterStartYear)
                ? $filterStartYear
                : Carbon::parse($endPeriod)->subYear()->format('Y');
        }

        $periodValues = $this->getPeriodValues(
            $startPeriod,
            $endPeriod,
            $isMonthly,
        );

        return [
            'periodValues' => $periodValues,
            'firstBranchId' => $firstBranchId,
            'secondBranchId' => $secondBranchId,
            'firstRegionId' => $firstRegionId,
            'secondRegionId' => $secondRegionId,
            'isMonthly' => $isMonthly,
        ];
    }

    public function getTopUnderFiveFilters(): array
    {
        $filterPeriod = request()->get('period');
        $filterYear = request()->get('year');
        $type = request()->get('type');

        $isMonthly = !empty($filterPeriod)
                || empty($filterYear);

        $periodValues = [];

        if ($isMonthly) {
            $period = (
                    isset($filterPeriod)
                    && !empty($filterPeriod)
                )
                ? $filterPeriod
                : Carbon::now()->format('Y-m');

            $period = collect(explode('-', $period))
                ->map(fn ($value): int => (int) $value);

            $periodValues['year'] = $period[0];
            $periodValues['month'] = $period[1];
        } else {
            $period = (
                    isset($filterYear)
                    && !empty($filterYear)
                )
                ? $filterYear
                : Carbon::now()->format('Y');

            $periodValues['year'] = $period;
            $periodValues['month'] = null;
        }

        return [
            'periodValues' => $periodValues,
            'type' => $type,
            'isMonthly' => $isMonthly,
        ];
    }
}