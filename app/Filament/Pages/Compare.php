<?php

namespace App\Filament\Pages;

use App\Models\Branch;
use App\Models\Region;
use App\Traits\HasReportFilter;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Page;

class Compare extends Page
{
    use HasFiltersForm, HasPageShield, HasReportFilter;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $navigationGroup = 'Data Analysis';

    protected static string $view = 'filament.pages.compare';

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
                ])->columns(2),
        ]);
    }

    protected function defaultBranchName(): string
    {
        return __('-');
    }

    protected function defaultRegionName(): string
    {
        return __('-');
    }
}
