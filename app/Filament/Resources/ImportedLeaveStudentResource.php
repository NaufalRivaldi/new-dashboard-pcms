<?php

namespace App\Filament\Resources;

use App\Enums\Month;
use App\Filament\Resources\ImportedLeaveStudentResource\Pages;
use App\Filament\Resources\ImportedLeaveStudentResource\RelationManagers;
use App\Models\ImportedLeaveStudent;
use App\Services\FilterService;
use App\Services\FormService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ImportedLeaveStudentResource extends ImportedNewStudentResource
{
    protected static ?string $model = ImportedLeaveStudent::class;

    protected static ?string $navigationLabel = 'LA13';

    protected static ?string $navigationIcon = 'heroicon-o-folder-arrow-down';

    protected static ?string $navigationGroup = 'Imports';

    protected static ?int $navigationSort = 7;

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
                        Forms\Components\Section::make('Date')->schema([
                            Forms\Components\Select::make('month')
                                ->options(Month::class)
                                ->searchable()
                                ->required()
                                ->disabledOn('edit')
                                ->live(onBlur: true),
                            Forms\Components\TextInput::make('year')
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
                        Forms\Components\Section::make('Details')->schema([
                            Forms\Components\TextInput::make('total')
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
                    ->searchable(isIndividual:true),
                Tables\Columns\TextColumn::make('month')
                    ->searchable(isIndividual:true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                    ->sortable()
                    ->searchable(isIndividual:true),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
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
            'index' => Pages\ListImportedLeaveStudents::route('/'),
            'create' => Pages\CreateImportedLeaveStudent::route('/create'),
            'edit' => Pages\EditImportedLeaveStudent::route('/{record}/edit'),
        ];
    }
}
