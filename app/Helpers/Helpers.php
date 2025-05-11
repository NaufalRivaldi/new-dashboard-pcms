<?php

use App\Enums\Month;
use App\Models\Branch;
use Illuminate\Support\Carbon;

if (! function_exists('periodFormatter')) {
    function periodFormatter(array $summary): string
    {
        return Month::name($summary['month']) . " {$summary['year']}";
    }
}

if (! function_exists('periodWithBranchFormatter')) {
    function periodWithBranchFormatter(array $summary): string
    {
        $branch = Branch::find($summary['branch_id'])->name ?? null;

        return Month::name($summary['month']) . " {$summary['year']} {$branch}";
    }
}

if (! function_exists('generateRandomHexColor')) {
    function generateRandomHexColor($characters = '0123456789ABCDEF'): string
    {
        $color = '#';

        for ($i = 0; $i < 6; $i++) {
            $color .= $characters[rand(0, 15)];
        }

        return $color;
    }
}

if (! function_exists('getFormattedPeriod')) {
    function getFormattedPeriod(?string $period = null): ?string
    {
        if (!is_null($period)) {
            return Carbon::parse($period)->format('F Y');
        }

        return null;
    }
}