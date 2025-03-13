<?php

namespace App\Filament\Pages;

use App\Models\Branch;
use App\Models\Region;
use App\Traits\HasReportFilter;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Page;

class Analysis extends Page
{
    use HasFiltersForm, HasPageShield, HasReportFilter;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $navigationGroup = 'Data Analysis';

    protected static string $view = 'filament.pages.analysis';

    public function persistsFiltersInSession(): bool
    {
        return false;
    }

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
                    Forms\Components\Select::make('branch_id')
                        ->label(__('Branch'))
                        ->options(
                            Branch::select([
                                    'id',
                                    'name',
                                ])
                                ->orderBy('name')
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->afterStateUpdated(function (callable $set) {
                            $set('region_id', null);
                        }),
                    Forms\Components\Select::make('region_id')
                        ->label(__('Region'))
                        ->options(
                            Region::select([
                                    'id',
                                    'name',
                                ])
                                ->orderBy('name')
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->afterStateUpdated(function (callable $set) {
                            $set('branch_id', null);
                        }),
                ])->columns(2),
        ]);
    }
}
