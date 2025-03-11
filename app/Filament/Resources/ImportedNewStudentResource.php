<?php

namespace App\Filament\Resources;

use App\Enums\Month;
use App\Filament\Resources\ImportedNewStudentResource\Pages;
use App\Filament\Resources\ImportedNewStudentResource\RelationManagers;
use App\Models\ImportedNewStudent;
use App\Services\FilterService;
use App\Services\FormService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ImportedNewStudentResource extends Resource
{
    protected static ?string $model = ImportedNewStudent::class;

    protected static ?string $navigationLabel = 'LA09';

    protected static ?string $navigationIcon = 'heroicon-o-folder-arrow-down';

    protected static ?string $navigationGroup = 'Imports';

    protected static ?int $navigationSort = 5;

    public static function getModelLabel(): string
    {
        return __('Import LA09');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\Grid::make(1)->schema([
                        Forms\Components\Section::make()->schema([
                            app(FormService::class)
                                ->branchSelectOption()
                                ->disabledOn('edit')
                                ->live(onBlur: true),
                        ]),
                        Forms\Components\Section::make(__('Date'))->schema([
                            Forms\Components\Select::make('month')
                                ->translateLabel()
                                ->options(Month::class)
                                ->searchable()
                                ->required()
                                ->disabledOn('edit')
                                ->live(onBlur: true),
                            Forms\Components\TextInput::make('year')
                                ->translateLabel()
                                ->required()
                                ->numeric()
                                ->minValue(2000)
                                ->maxValue(2030)
                                ->disabledOn('edit')
                                ->live(onBlur: true),
                        ]),
                    ])->columnSpan([
                        'default' => 3,
                        'lg' => 1,
                    ]),
                    Forms\Components\Grid::make(1)->schema([
                        Forms\Components\Section::make(__('Details'))->schema([
                            Forms\Components\TextInput::make('total')
                                ->translateLabel()
                                ->required()
                                ->numeric()
                                ->suffix('Stundents')
                                ->default(0),
                        ]),
                    ])->columnSpan([
                        'default' => 3,
                        'lg' => 2,
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $filterService = app(FilterService::class);

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('branch.name')
                    ->translateLabel()
                    ->searchable(isIndividual:true),
                Tables\Columns\TextColumn::make('month')
                    ->translateLabel()
                    ->formatStateUsing(fn (string $state): string => Month::name($state))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                    ->translateLabel()
                    ->sortable()
                    ->searchable(isIndividual:true),
                Tables\Columns\TextColumn::make('total')
                    ->translateLabel()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->translateLabel()
                    ->numeric()
                    ->searchable(isIndividual:true)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                $filterService->filterByBranch(),
                $filterService->filterByMonth(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListImportedNewStudents::route('/'),
            'create' => Pages\CreateImportedNewStudent::route('/create'),
            'edit' => Pages\EditImportedNewStudent::route('/{record}/edit'),
        ];
    }
}
