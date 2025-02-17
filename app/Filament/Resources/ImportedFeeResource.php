<?php

namespace App\Filament\Resources;

use App\Enums\Month;
use App\Enums\PaymentType;
use App\Filament\Resources\ImportedFeeResource\Pages;
use App\Filament\Resources\ImportedFeeResource\RelationManagers;
use App\Models\ImportedFee;
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

class ImportedFeeResource extends Resource
{
    protected static ?string $model = ImportedFee::class;

    protected static ?string $navigationLabel = 'LA03';

    protected static ?string $navigationIcon = 'heroicon-o-folder-arrow-down';

    protected static ?string $navigationGroup = 'Imports';

    protected static ?int $navigationSort = 2;

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
                                ->prefix('Rp.')
                                ->default(0),
                            Forms\Components\Repeater::make('details')
                                ->label('')
                                ->relationship('details')
                                ->schema([
                                    Forms\Components\Select::make('type')
                                        ->options(PaymentType::class)
                                        ->searchable()
                                        ->required(),
                                    Forms\Components\TextInput::make('payer_name'),
                                    Forms\Components\TextInput::make('nominal')
                                        ->required()
                                        ->numeric()
                                        ->prefix('Rp.')
                                        ->default(0)
                                        ->live(onBlur: true),
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
                                ])
                                ->addAction(function (Get $get, Set $set) {
                                    $total = collect($get('details'))
                                        ->pluck('nominal')
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
                    ->searchable(isIndividual:true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                    ->sortable()
                    ->searchable(isIndividual:true),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->prefix('Rp. ')
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
            'index' => Pages\ListImportedFees::route('/'),
            'create' => Pages\CreateImportedFee::route('/create'),
            'edit' => Pages\EditImportedFee::route('/{record}/edit'),
        ];
    }
}
