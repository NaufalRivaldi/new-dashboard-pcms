<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchResource\Pages;
use App\Filament\Resources\BranchResource\RelationManagers;
use App\Models\Branch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Locations';

    public static function getModelLabel(): string
    {
        return __('Branch');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Details'))
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->translateLabel()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name')
                            ->translateLabel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('region_id')
                            ->label(__('Region'))
                            ->relationship(
                                name: 'region',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query) {
                                    return $query
                                        ->select('id', 'name');
                                }
                            )
                            ->searchable()
                            ->preload(),
                    ])->columns(2),
                Forms\Components\Section::make(__('Location'))
                    ->description(__('Set the location to add a pinpoint on the map.'))
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('longitude')
                            ->maxLength(255),
                    ])->columns(2),
                Forms\Components\Section::make('Others')
                    ->schema([
                        Forms\Components\Repeater::make('ownerships')
                            ->translateLabel()
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label(__('Select user'))
                                    ->relationship(
                                        name: 'user',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: function (Builder $query) {
                                            return $query
                                                ->select('id', 'name')
                                                ->role([
                                                    'Owner'
                                                ]);
                                        }
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ])
                            ->grid(2),
                    ])->columns(1),
            ]);
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
                Tables\Columns\TextColumn::make('latitude')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('longitude')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('region.name')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('ownerships_count')
                    ->label(__('Total Ownerships'))
                    ->counts('ownerships')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('region_id')
                    ->label(__('Region'))
                    ->multiple()
                    ->relationship(
                        name: 'region',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query) {
                            return $query
                                ->select('id', 'name');
                        }
                    )
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('The number of branches');
    }
}
