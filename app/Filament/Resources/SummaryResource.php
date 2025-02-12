<?php

namespace App\Filament\Resources;

use App\Enums\Month;
use App\Filament\Resources\SummaryResource\Pages;
use App\Filament\Resources\SummaryResource\RelationManagers;
use App\Models\Summary;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SummaryResource extends Resource
{
    protected static ?string $model = Summary::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Imports';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\Grid::make(1)->schema([
                        Forms\Components\Section::make()->schema([
                            Forms\Components\Select::make('branch_id')
                                ->label(__('Branch'))
                                ->relationship(
                                    name: 'branch',
                                    titleAttribute: 'name',
                                    modifyQueryUsing: function (Builder $query) {
                                        return $query
                                            ->select('id', 'name');
                                    }
                                )
                                ->searchable()
                                ->preload()
                                ->disabledOn('edit')
                                ->live(onBlur: true),
                            Forms\Components\Toggle::make('status')
                                ->label(__('Is Approved?'))
                                ->required(),
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
                        Forms\Components\Section::make('Fee')
                            ->schema([
                                Forms\Components\TextInput::make('registration_fee')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->live(onBlur: true),
                                Forms\Components\TextInput::make('course_fee')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->live(onBlur: true),
                                Forms\Components\TextInput::make('total_fee')
                                    ->helperText(__('Calculate automated.'))
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->readonly(),
                                Forms\Components\TextInput::make('royalty')
                                    ->label(__('Royalty (10%)'))
                                    ->helperText(__('Calculate automated.'))
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->readonly(),
                            ])
                            ->columns(2),
                        Forms\Components\Section::make('Student')
                            ->schema([
                                Forms\Components\TextInput::make('active_student')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\TextInput::make('new_student')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\TextInput::make('inactive_student')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\TextInput::make('leave_student')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->columns(2),
                        Forms\Components\Section::make('Student Active Based On Education')
                            ->schema([
                                Forms\Components\Repeater::make('summary_active_student_education')
                                    ->label('')
                                    ->relationship('summaryActiveStudentEducation')
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
                                            ->required()
                                            ->numeric()
                                            ->suffix('Students')
                                            ->default(0),
                                    ])
                                    ->columns(2),
                            ]),
                        Forms\Components\Section::make('Student Active Based On Lesson')
                            ->schema([
                                Forms\Components\Repeater::make('summary_active_student_lesson')
                                    ->label('')
                                    ->relationship('summaryActiveStudentLesson')
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
                                            ->suffix('Students')
                                            ->default(0),
                                    ])
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
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('month')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year')
                    ->searchable(),
                Tables\Columns\TextColumn::make('registration_fee')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('course_fee')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_fee')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('royalty')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student_active')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student_new')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student_out')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student_leave')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('branch_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('approver_id')
                    ->numeric()
                    ->sortable(),
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
                //
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
            'index' => Pages\ListSummaries::route('/'),
            'create' => Pages\CreateSummary::route('/create'),
            'edit' => Pages\EditSummary::route('/{record}/edit'),
        ];
    }
}
