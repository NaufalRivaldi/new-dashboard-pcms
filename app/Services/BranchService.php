<?php

namespace App\Services;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class BranchService
{
    public function getBranchNotImportedByPeriod(?string $period = null): Collection
    {
        $period = is_null($period) || $period == ''
            ? now()->format('Y-m')
            : $period;

        $date = collect(explode('-', $period))
            ->map(fn ($value) => (int) $value)
            ->all();


        return Branch::query()
            ->select([
                'code',
                'name',
                'region_id',
            ])
            ->whereDoesntHave('summaries', function (Builder $query) use ($date) {
                $query
                    ->where('year', $date[0])
                    ->where('month', $date[1]);
            })
            ->with(['region:id,name'])
            ->orderBy('code')
            ->get();
    }
}