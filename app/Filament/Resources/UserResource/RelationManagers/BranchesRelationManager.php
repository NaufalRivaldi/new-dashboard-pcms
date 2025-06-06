<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BranchesRelationManager extends RelationManager
{
    protected static string $relationship = 'branches';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Branches');
    }

    public function form(Form $form): Form
    {
        return $form
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->translateLabel()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('region.name')
                    ->translateLabel(),
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
            ]);
    }
}
