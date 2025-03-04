<?php

namespace App\Filament\Pages\Reports;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class TopOrUnderFive extends Page
{
    use HasFiltersForm;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $navigationGroup = 'Reports';

    protected static string $view = 'filament.pages.reports.top-five';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Top or Under 5';

    public function filtersForm(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Filter Data')
                ->schema([
                    Forms\Components\TextInput::make('period')
                        ->type('month'),
                    Forms\Components\Select::make('type')
                        ->options([
                            'top' => 'Top 5',
                            'under' => 'Under 5',
                        ]),
                ])->columns(3),
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
