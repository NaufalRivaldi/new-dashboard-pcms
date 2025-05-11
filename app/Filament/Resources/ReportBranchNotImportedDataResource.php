<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportBranchNotImportedDataResource\Pages;
use App\Filament\Resources\ReportBranchNotImportedDataResource\RelationManagers;
use App\Models\Branch;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class ReportBranchNotImportedDataResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('Unimported Branches');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->translateLabel()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('region.name')
                    ->translateLabel()
                    ->searchable(isIndividual: true),
            ])
            ->filters([
                Filter::make('period')
                    ->form([
                        TextInput::make('period')
                            ->translateLabel()
                            ->type('month'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->where(function (Builder $query) use ($data) {
                                $date = collect(explode('-', $data['period'] ?? Carbon::now()->format('Y-m')))
                                    ->map(fn ($value) => (int) $value)
                                    ->all();

                                return $query->whereDoesntHave('summaries', function (Builder $query) use ($date) {
                                    $query
                                        ->where('year', $date[0])
                                        ->where('month', $date[1]);
                                });
                            });
                    })
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->actions([])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReportBranchNotImportedData::route('/'),
        ];
    }
}
