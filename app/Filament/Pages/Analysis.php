<?php

namespace App\Filament\Pages;

use App\Models\Branch;
use App\Models\Region;
use App\Traits\HasReportFilter;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Forms\Get;
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
            Tabs::make('Tabs')
                ->tabs([
                    Tabs\Tab::make('Monthly')
                        ->translateLabel()
                        ->schema([
                            Forms\Components\TextInput::make('start_period')
                                ->translateLabel()
                                ->type('month')
                                ->afterStateUpdated(function (callable $set) {
                                    $set('start_year', null);
                                    $set('end_year', null);
                                }),
                            Forms\Components\TextInput::make('end_period')
                                ->translateLabel()
                                ->type('month')
                                ->afterStateUpdated(function (callable $set) {
                                    $set('start_year', null);
                                    $set('end_year', null);
                                }),
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
                        ])
                        ->columns(2),
                    Tabs\Tab::make('Yearly')
                        ->translateLabel()
                        ->schema([
                            Forms\Components\Select::make('start_year')
                                ->options(function (Get $get) {
                                    $endYear = $get('end_year');

                                    return collect(range($endYear ?? now()->year, 2000))
                                        ->mapWithKeys(fn ($year) => [$year => $year])
                                        ->toArray();
                                })
                                ->searchable()
                                ->afterStateUpdated(function (callable $set) {
                                    $set('start_period', null);
                                    $set('end_period', null);
                                }),
                            Forms\Components\Select::make('end_year')
                                ->options(function (Get $get) {
                                    $startYear = $get('start_year');

                                    return collect(range(now()->year, $startYear ?? 2000))
                                        ->mapWithKeys(fn ($year) => [$year => $year])
                                        ->toArray();
                                })
                                ->searchable()
                                ->afterStateUpdated(function (callable $set) {
                                    $set('start_period', null);
                                    $set('end_period', null);
                                }),
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
                        ])
                        ->columns(2),
                    ])
                    ->columnSpanFull(),
        ]);
    }
}
