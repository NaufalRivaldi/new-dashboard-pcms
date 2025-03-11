<?php

namespace App\Services;

use App\Enums\Month;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class FilterService
{
    public function filterByBranch(bool $isMultiple = true): SelectFilter
    {
        return SelectFilter::make('branch_id')
            ->label(__('Branch'))
            ->multiple($isMultiple)
            ->relationship(
                name: 'branch',
                titleAttribute: 'name',
                modifyQueryUsing: function (Builder $query) {
                    return $query
                        ->select('id', 'name');
                }
            )
            ->searchable()
            ->preload();
    }

    public function filterByMonth(bool $isMultiple = true)
    {
        return SelectFilter::make('month')
            ->translateLabel()
            ->options(Month::class)
            ->multiple($isMultiple)
            ->searchable();
    }
}