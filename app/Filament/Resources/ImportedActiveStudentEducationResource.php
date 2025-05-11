<?php

namespace App\Filament\Resources;

use App\Enums\Month;
use App\Filament\Resources\ImportedActiveStudentEducationResource\Pages;
use App\Filament\Resources\ImportedActiveStudentEducationResource\RelationManagers;
use App\Models\ImportedActiveStudentEducation;
use App\Services\FilterService;
use App\Services\FormService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class ImportedActiveStudentEducationResource extends Resource
{
    protected static ?string $model = ImportedActiveStudentEducation::class;

    protected static ?string $navigationLabel = 'LA07';

    protected static ?string $navigationIcon = 'heroicon-o-folder-arrow-down';

    protected static ?string $navigationGroup = 'Imports';

    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return __('Import LA07');
    }

    public static function form(Form $form): Form
    {
        $defaultBranchId = null;
        $currentDate = Carbon::now();
        $user = auth()->user();

        if (!$user->isSuperAdminOrAdmin) {
            $defaultBranchId = $user->branch?->id;
        }

        return $form
            ->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\Grid::make(1)->schema([
                        Forms\Components\Section::make()->schema([
                            app(FormService::class)
                                ->branchSelectOption()
                                ->disabledOn('edit')
                                ->live(onBlur: true)
                                ->default($defaultBranchId)
                                ->disabled(!$user->isSuperAdminOrAdmin),
                        ]),
                        Forms\Components\Section::make(__('Date'))->schema([
                            Forms\Components\Select::make('month')
                                ->translateLabel()
                                ->options(Month::class)
                                ->default((int)$currentDate->format('m'))
                                ->searchable()
                                ->required()
                                ->disabledOn('edit')
                                ->live(onBlur: true),
                            Forms\Components\TextInput::make('year')
                                ->translateLabel()
                                ->required()
                                ->default((int)$currentDate->format('Y'))
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
                                ->readOnly()
                                ->suffix('Stundents')
                                ->default(0),
                            Forms\Components\Repeater::make('details')
                                ->label('')
                                ->relationship('details')
                                ->schema([
                                    Forms\Components\Select::make('education_id')
                                        ->label(__('Education'))
                                        ->relationship(
                                            name: 'education',
                                            titleAttribute: 'name',
                                            modifyQueryUsing: function (Builder $query) {
                                                return $query->select('id', 'name');
                                            }
                                        )
                                        ->searchable()
                                        ->preload(),
                                    Forms\Components\TextInput::make('total')
                                        ->translateLabel()
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
            'index' => Pages\ListImportedActiveStudentEducation::route('/'),
            'create' => Pages\CreateImportedActiveStudentEducation::route('/create'),
            'edit' => Pages\EditImportedActiveStudentEducation::route('/{record}/edit'),
        ];
    }
}
