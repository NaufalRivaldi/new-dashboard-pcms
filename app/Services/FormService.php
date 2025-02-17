<?php

namespace App\Services;

use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FormService
{
    public function branchSelectOption(): Select
    {
        return Select::make('branch_id')
            ->label(__('Branch'))
            ->relationship(
                name: 'branch',
                modifyQueryUsing: function (Builder $query) {
                    return $query
                        ->select('id', 'code', 'name');
                }
            )
            ->getOptionLabelFromRecordUsing(fn (Model $record) => "[{$record->code}] {$record->name}")
            ->searchable()
            ->preload()
            ->required();
    }
}