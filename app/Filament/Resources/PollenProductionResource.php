<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PollenProductionResource\Pages;
use App\Models\PollenProduction;
use App\Filament\Traits\HasApprovalActions;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PollenProductionResource extends Resource implements HasShieldPermissions
{
    use HasApprovalActions;
    protected static ?string $model = PollenProduction::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static ?string $navigationGroup = 'Field Data';

    protected static ?string $navigationLabel = 'Pollen Production';

    protected static ?int $navigationSort = 6;

    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pollen Details')
                    ->icon('heroicon-o-beaker')
                    ->schema([
                        Forms\Components\Grid::make(4)->schema([
                            Forms\Components\Group::make([
                                Forms\Components\Select::make('field_site_id')
                                    ->label('Field Site')
                                    ->relationship('fieldSite', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->default(fn () => auth()->user()->field_site_id)
                                    ->disabled(fn () => auth()->user()?->isSupervisor())
                                    ->visible(fn () => !auth()->user()?->isSupervisor())
                                    ->dehydrated()
                                    ->columnSpanFull(),

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('loadPrevious')
                                        ->label('Load from Previous Month')
                                        ->icon('heroicon-o-arrow-path')
                                        ->color('success')
                                        ->outlined()
                                        ->size('sm')
                                        ->action(function (Forms\Set $set, Forms\Get $get) {
                                            $siteId = $get('field_site_id') ?: auth()->user()->field_site_id;
                                            if (!$siteId) {
                                                \Filament\Notifications\Notification::make()->warning()->title('Please select a Field Site first.')->send();
                                                return;
                                            }
                                            
                                            $latest = \App\Models\PollenProduction::where('field_site_id', $siteId)
                                                ->orderBy('report_month', 'desc')
                                                ->first();
                                                
                                            if (!$latest) {
                                                \Filament\Notifications\Notification::make()->warning()->title('No previous records found for this site.')->send();
                                                return;
                                            }
                                            
                                            if ($latest->report_month) {
                                                $set('report_month', $latest->report_month->copy()->addMonth()->startOfMonth()->format('Y-m-d'));
                                            }
                                            $set('pollen_variety', $latest->pollen_variety);
                                            $set('ending_balance_prev', $latest->ending_balance);
                                            
                                            \Filament\Notifications\Notification::make()->success()->title('Loaded from previous record.')->send();
                                        })
                                ]),
                            ])->columnSpan(1),

                            Forms\Components\DatePicker::make('report_month')
                                ->label('Report Month')
                                ->required()
                                ->displayFormat('m / d / Y')
                                ->default(now()->startOfMonth()),

                            Forms\Components\Select::make('month_label')
                                ->label('Month Label')
                                ->options([
                                    'January' => 'January', 'February' => 'February', 'March' => 'March',
                                    'April' => 'April', 'May' => 'May', 'June' => 'June',
                                    'July' => 'July', 'August' => 'August', 'September' => 'September',
                                    'October' => 'October', 'November' => 'November', 'December' => 'December',
                                ])
                                ->placeholder('— Select Month —'),
                            Forms\Components\TextInput::make('pollen_variety')
                                ->label('Pollen Variety')
                                ->placeholder('e.g. LAGUNA TALL POLLENS')
                                ->maxLength(200),
                            Forms\Components\TextInput::make('ending_balance_prev')
                                ->label('Ending Balance (Last Month)')
                                ->numeric()
                                ->maxLength(50)
                                ->columnSpan(1),
                        ]),

                        Forms\Components\Placeholder::make('divider_1')
                            ->hiddenLabel()
                            ->content(new \Illuminate\Support\HtmlString('<hr class="border-gray-200 dark:border-gray-700"><h3 class="text-base font-medium text-gray-900 dark:text-white mt-4">Pollens Received from Other Center</h3>'))
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('pollen_source')
                                ->label('Source')
                                ->placeholder('e.g. CVSPC')
                                ->maxLength(200),
                            Forms\Components\DatePicker::make('date_received')
                                ->label('Date Received')
                                ->displayFormat('m / d / Y'),
                            Forms\Components\TextInput::make('pollens_received')
                                ->label('Amount of Pollens')
                                ->numeric()
                                ->maxLength(50),
                        ]),

                        Forms\Components\Placeholder::make('divider_2')
                            ->hiddenLabel()
                            ->content(new \Illuminate\Support\HtmlString('<hr class="border-gray-200 dark:border-gray-700"><h3 class="text-base font-medium text-gray-900 dark:text-white mt-4">Pollen Utilization (grams per Week)</h3>'))
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(6)->schema([
                            Forms\Components\TextInput::make('week1')->label('Week 1')->numeric()->maxLength(20),
                            Forms\Components\TextInput::make('week2')->label('Week 2')->numeric()->maxLength(20),
                            Forms\Components\TextInput::make('week3')->label('Week 3')->numeric()->maxLength(20),
                            Forms\Components\TextInput::make('week4')->label('Week 4')->numeric()->maxLength(20),
                            Forms\Components\TextInput::make('week5')->label('Week 5')->numeric()->maxLength(20),
                            Forms\Components\TextInput::make('total_utilization')
                                ->label('Total Utilization')
                                ->numeric()
                                ->maxLength(20),
                        ]),

                        Forms\Components\Placeholder::make('divider_3')
                            ->hiddenLabel()
                            ->content(new \Illuminate\Support\HtmlString('<hr class="border-gray-200 dark:border-gray-700">'))
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\Grid::make(1)->schema([
                                Forms\Components\TextInput::make('ending_balance')
                                    ->label('Ending Balance')
                                    ->numeric()
                                    ->maxLength(50),
                            ])->columnSpan(1),
                            Forms\Components\Textarea::make('remarks')
                                ->label('Remarks')
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fieldSite.name')
                    ->label('Field Site')
                    ->sortable(),
                Tables\Columns\TextColumn::make('report_month')
                    ->date('F Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pollen_variety')
                    ->label('Variety')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ending_balance_prev')
                    ->label('Prev Balance')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('pollens_received')
                    ->label('Received'),
                Tables\Columns\TextColumn::make('total_utilization')
                    ->label('Total Utilized'),
                Tables\Columns\TextColumn::make('ending_balance')
                    ->label('End Balance'),
                self::getStatusColumn(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('field_site_id')
                    ->label('Field Site')
                    ->relationship('fieldSite', 'name'),
                Tables\Filters\Filter::make('report_year')
                    ->form([
                        Forms\Components\Select::make('year')
                            ->options(fn () => collect(range(now()->year, 2024, -1))
                                ->mapWithKeys(fn ($y) => [$y => $y]))
                            ->default(now()->year),
                    ])
                    ->query(fn (Builder $query, array $data) =>
                        $query->when($data['year'] ?? null, fn ($q, $year) =>
                            $q->whereYear('report_month', $year)
                        )
                    ),
                self::getStatusFilter(),
            ])
            ->defaultSort('report_month', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                ...self::getApprovalActions(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->exporter(\App\Filament\Exports\PollenProductionExporter::class),
                Tables\Actions\Action::make('formattedExport')
                    ->label('Formatted Export (Excel)')
                    ->icon('heroicon-o-document-chart-bar')
                    ->color('success')
                    ->action(function (Tables\Table $table) {
                        $records = $table->getQuery()->get();
                        return (new \App\Exports\PollenProductionExport($records))->export();
                    }),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if (auth()->user()?->isSupervisor()) {
            $query->where('field_site_id', auth()->user()->field_site_id);
        }
        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPollenProductions::route('/'),
            'create' => Pages\CreatePollenProduction::route('/create'),
            'edit' => Pages\EditPollenProduction::route('/{record}/edit'),
        ];
    }
}
