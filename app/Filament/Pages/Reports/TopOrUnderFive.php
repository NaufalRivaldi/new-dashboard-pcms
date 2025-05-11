<?php

namespace App\Filament\Pages\Reports;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class TopOrUnderFive extends Page
{
    use HasFiltersForm, HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $navigationGroup = 'Reports';

    protected static string $view = 'filament.pages.reports.top-five';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Top or Under 5';

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
                            Forms\Components\TextInput::make('period')
                                ->translateLabel()
                                ->type('month')
                                ->afterStateUpdated(function (callable $set) {
                                    $set('year', null);
                                }),
                            Forms\Components\Select::make('type')
                                ->translateLabel()
                                ->options([
                                    'top' => 'Top 5',
                                    'under' => 'Under 5',
                                ]),
                        ])
                        ->columns(2),
                    Tabs\Tab::make('Yearly')
                        ->translateLabel()
                        ->schema([
                            Forms\Components\Select::make('year')
                                ->options(function () {
                                    return collect(range(now()->year, 2000))
                                        ->mapWithKeys(fn ($year) => [$year => $year])
                                        ->toArray();
                                })
                                ->searchable()
                                ->afterStateUpdated(function (callable $set) {
                                    $set('period', null);
                                }),
                            Forms\Components\Select::make('type')
                                ->translateLabel()
                                ->options([
                                    'top' => 'Top 5',
                                    'under' => 'Under 5',
                                ]),
                        ])
                        ->columns(2),
                    ])
                    ->columnSpanFull(),
        ]);
    }

    public function getFormattedPeriod(?string $period = null): ?string
    {
        if (!is_null($period)) {
            return Carbon::parse($period)->format('F Y');
        }

        return '-';
    }
}
