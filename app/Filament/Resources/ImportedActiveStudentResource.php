<?php

namespace App\Filament\Resources;

use App\Enums\Month;
use App\Filament\Resources\ImportedActiveStudentResource\Pages;
use App\Filament\Resources\ImportedActiveStudentResource\RelationManagers;
use App\Models\ImportedActiveStudent;
use App\Services\FilterService;
use App\Services\FormService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ImportedActiveStudentResource extends Resource
{
    protected static ?string $model = ImportedActiveStudent::class;

    protected static ?string $navigationLabel = 'LA06';

    protected static ?string $navigationIcon = 'heroicon-o-folder-arrow-down';

    protected static ?string $navigationGroup = 'Imports';

    protected static ?int $navigationSort = 3;

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
                                ->readOnly()
                                ->suffix('Stundents')
                                ->default(0),
                            Forms\Components\Repeater::make('details')
                                ->label('')
                                ->relationship('details')
                                ->schema([
                                    Forms\Components\Select::make('lesson_id')
                                        ->label(__('Lesson'))
                                        ->relationship(
                                            name: 'lesson',
                                            titleAttribute: 'name',
                                            modifyQueryUsing: function (Builder $query) {
                                                return $query->select('id', 'name');
                                            }
                                        )
                                        ->searchable()
                                        ->preload(),
                                    Forms\Components\TextInput::make('total')
                                        ->required()
                                        ->numeric()
                                        ->suffix('Stundents')
                                        ->default(0)
                                        ->live(onBlur: true),
                                ])
                                ->addAction(function (Get $get, Set $set) {
                                    $total = collect($get('details'))
                                        ->pluck('total')
                                        ->sum();

                                    $set('total', $total);
                                })
                                ->columns(2),
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
                    ->formatStateUsing(fn (string $state): string => Month::name($state))
                    ->searchable()
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
            'index' => Pages\ListImportedActiveStudents::route('/'),
            'create' => Pages\CreateImportedActiveStudent::route('/create'),
            'edit' => Pages\EditImportedActiveStudent::route('/{record}/edit'),
        ];
    }
}
