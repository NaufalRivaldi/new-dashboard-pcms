<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Services\FilterService;
use App\Services\FormService;
use App\Services\NotificationService;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Masters';

    public static function getModelLabel(): string
    {
        return __('User');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Personal Details'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->translateLabel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->translateLabel()
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('roles')
                            ->translateLabel()
                            ->relationship(
                                name: 'roles',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query) {
                                    if (!auth()->user()->isSuperAdmin) {
                                        return $query->whereNotIn('name', ['super_admin']);
                                    }

                                    return $query;
                                }
                            )

                            ->preload()
                            ->searchable(),
                        resolve(FormService::class)->branchSelectOption(),
                    ])
                    ->columns(2),
                Section::make('Auth')
                    ->description(__('The field required for authentication purposes.'))
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required()
                            ->maxLength(255),
                    ])
                    ->hidden(fn (Get $get) => !is_null($get('id'))),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->searchable(isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('email')
                    ->translateLabel()
                    ->searchable(isIndividual: true),
                Tables\Columns\ToggleColumn::make('status')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('roles')
                    ->translateLabel()
                    ->formatStateUsing(fn (string $state): string => json_decode($state, true)['name']),
                Tables\Columns\TextColumn::make('branch.name')
                    ->translateLabel()
                    ->searchable(),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                if (!auth()->user()->isSuperAdmin) {
                    return $query->withoutRole('super_admin');
                }
            })
            ->filters([
                SelectFilter::make('roles')
                    ->translateLabel()
                    ->multiple()
                    ->preload()
                    ->relationship(
                        name: 'roles',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query) {
                            if (!auth()->user()->isSuperAdmin) {
                                return $query->whereNotIn('name', ['super_admin']);
                            }

                            return $query;
                        }
                    ),
                resolve(FilterService::class)->filterByBranch(),
            ])
            ->actions([
                Tables\Actions\Action::make('resetPassword')
                    ->requiresConfirmation()
                    ->icon('heroicon-m-cog-6-tooth')
                    ->color('danger')
                    ->action(function (Model $record,) {
                        $record->password = Hash::make('password');
                        $record->save();

                        NotificationService::success(
                            title: __('Reset password successfully!'),
                        );
                    }),
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
            // RelationManagers\BranchesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        if (!auth()->user()->isSuperAdmin) {
            return static::getModel()::withoutRole('super_admin')->count();
        }

        return static::getModel()::count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('The number of users');
    }
}
