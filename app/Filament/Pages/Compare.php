<?php

namespace App\Filament\Pages;

use App\Models\Branch;
use App\Models\Region;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Page;

class Compare extends Page
{
    use HasFiltersForm, HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $navigationGroup = 'Data Analysis';

    protected static string $view = 'filament.pages.compare';

    public function filtersForm(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('Filter'))
                ->schema([
                    Forms\Components\TextInput::make('start_period')
                        ->translateLabel()
                        ->type('month'),
                    Forms\Components\TextInput::make('end_period')
                        ->translateLabel()
                        ->type('month'),
                    Forms\Components\Select::make('first_branch_id')
                        ->label(__('First branch'))
                        ->options(
                            Branch::select([
                                    'id',
                                    'name',
                                ])
                                ->orderBy('name')
                                ->pluck('name', 'id')
                        )
                        ->searchable(),
                    Forms\Components\Select::make('second_branch_id')
                        ->label(__('Second branch'))
                        ->options(
                            Branch::select([
                                    'id',
                                    'name',
                                ])
                                ->orderBy('name')
                                ->pluck('name', 'id')
                        )
                        ->searchable(),
                    Forms\Components\Select::make('first_region_id')
                        ->label(__('First region'))
                        ->options(
                            Region::select([
                                    'id',
                                    'name',
                                ])
                                ->orderBy('name')
                                ->pluck('name', 'id')
                        )
                        ->searchable(),
                    Forms\Components\Select::make('second_region_id')
                        ->label(__('Second region'))
                        ->options(
                            Region::select([
                                    'id',
                                    'name',
                                ])
                                ->orderBy('name')
                                ->pluck('name', 'id')
                        )
                        ->searchable(),
                ])->columns(2),
        ]);
    }

    public function getBranchName(?int $branchId = null): string
    {
        if (!is_null($branchId)) {
            return Branch::find($branchId)->name ?? '-';
        }

        return '-';
    }

    public function getFormattedPeriod(?string $period = null): ?string
    {
        if (!is_null($period)) {
            return Carbon::parse($period)->format('F Y');
        }

        return null;
    }
}
