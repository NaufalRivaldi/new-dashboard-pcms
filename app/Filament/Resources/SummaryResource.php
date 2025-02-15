<?php

namespace App\Filament\Resources;

use App\Enums\Month;
use App\Filament\Resources\SummaryResource\Pages;
use App\Filament\Resources\SummaryResource\RelationManagers;
use App\Models\Summary;
use App\Services\FilterService;
use App\Services\ImportService;
use App\Services\NotificationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Torgodly\Html2Media\Tables\Actions\Html2MediaAction;

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
                                ->required()
                                ->hidden(
                                    !auth()->user()->isAdmin
                                ),
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
                            Forms\Components\Actions::make([
                                Forms\Components\Actions\Action::make('Generate')
                                    ->icon('heroicon-m-arrow-path')
                                    ->hidden(fn (Get $get) => !is_null($get('id')))
                                    ->action(function (Get $get, Set $set) {
                                        $branchId = $get('branch_id');
                                        $month = $get('month');
                                        $year = $get('year');

                                        if (
                                            is_null($branchId)
                                            || is_null($month)
                                            || is_null($year)
                                        ) {
                                            NotificationService::warning(
                                                title: __('Some data is still empty!'),
                                                body: __('Make sure the branch, month, and year is get filled.'),
                                            );
                                        } else {
                                            $result = app(ImportService::class)->generateSummaryData(
                                                $branchId,
                                                $month,
                                                $year,
                                            );

                                            if ($result['status']) {

                                                $set('registration_fee', $result['registration_fee']);
                                                $set('course_fee', $result['course_fee']);
                                                $set('total_fee', $result['total_fee']);
                                                $set('royalty', $result['royalty']);
                                                $set('active_student', $result['active_student']);
                                                $set('new_student', $result['new_student']);
                                                $set('inactive_student', $result['inactive_student']);
                                                $set('leave_student', $result['leave_student']);
                                                $set('summary_active_student_education', $result['summary_active_student_education']);
                                                $set('summary_active_student_lesson', $result['summary_active_student_lesson']);

                                            } else {

                                                NotificationService::warning(
                                                    title: __('Generate data failed!'),
                                                    body: $result['message'],
                                                );

                                            }
                                        }
                                    }),
                            ]),
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
                                    ->prefix('Rp.')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $calculationResult = app(ImportService::class)->calculateTotalAndRoyaltyFee(
                                            (float) $get('registration_fee'),
                                            (float) $get('course_fee'),
                                        );

                                        $set('total_fee', $calculationResult['total']);
                                        $set('royalty', $calculationResult['royalty']);
                                    }),
                                Forms\Components\TextInput::make('course_fee')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->prefix('Rp.')
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $calculationResult = app(ImportService::class)->calculateTotalAndRoyaltyFee(
                                            (float) $get('registration_fee'),
                                            (float) $get('course_fee'),
                                        );

                                        $set('total_fee', $calculationResult['total']);
                                        $set('royalty', $calculationResult['royalty']);
                                    }),
                                Forms\Components\TextInput::make('total_fee')
                                    ->helperText(__('Calculate automated.'))
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('Rp.')
                                    ->readonly(),
                                Forms\Components\TextInput::make('royalty')
                                    ->label(__('Royalty (10%)'))
                                    ->helperText(__('Calculate automated.'))
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('Rp.')
                                    ->readonly(),
                            ])
                            ->columns(2),
                        Forms\Components\Section::make('Student')
                            ->schema([
                                Forms\Components\TextInput::make('active_student')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->suffix(__('Students')),
                                Forms\Components\TextInput::make('new_student')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->suffix(__('Students')),
                                Forms\Components\TextInput::make('inactive_student')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->suffix(__('Students')),
                                Forms\Components\TextInput::make('leave_student')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->suffix(__('Students')),
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
        $filterService = app(FilterService::class);

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('branch.name')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('month')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('status')
                    ->label(__('Is Approved?'))
                    ->tooltip(
                        __('Only user with :roles roles can approved the summary', [
                            'roles' => implode(', ', config('permission.approver_roles'))
                        ])
                    )
                    ->disabled(!auth()->user()->isApprover)
                    ->afterStateUpdated(function ($record, $state) {
                        $userId = auth()->user()->id;

                        if ($state) {
                            $record->approver_id = $userId;
                        } else {
                            $record->approver_id = null;
                        }

                        $record->save();
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('approver.name')
                    ->searchable(isIndividual: true),
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
                Tables\Actions\ViewAction::make()
                    ->extraModalFooterActions([
                        Html2MediaAction::make('print')
                            ->filename('summary-export')
                            ->pagebreak('section', ['css', 'legacy'])
                            ->margin([10, 10, 10, 10])
                            ->preview()
                            ->savePdf()
                            ->content(fn (Summary $record) => view('pdf.summary', ['record' => $record]))
                            ->icon('heroicon-m-printer')
                            ->hidden(fn (Summary $record): bool => !$record->isApproved),
                    ]),
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
            'index' => Pages\ListSummaries::route('/'),
            'create' => Pages\CreateSummary::route('/create'),
            'edit' => Pages\EditSummary::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::pending()->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('The number of pending summaries');
    }
}
