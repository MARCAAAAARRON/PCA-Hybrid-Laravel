<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HybridizationRecordResource\Pages;
use App\Models\HybridizationRecord;
use App\Filament\Traits\HasApprovalActions;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HybridizationRecordResource extends Resource implements HasShieldPermissions
{
    use HasApprovalActions;
    protected static ?string $model = HybridizationRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static ?string $navigationGroup = 'Hybridization';

    protected static ?string $navigationLabel = 'Hybridization Records';

    protected static ?int $navigationSort = 1;

    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Record Details')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        Forms\Components\Select::make('field_site_id')
                            ->label('Field Site')
                            ->relationship('fieldSite', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->default(fn () => auth()->user()->field_site_id)
                            ->disabled(fn () => auth()->user()?->isSupervisor())
                            ->dehydrated(),
                        Forms\Components\Hidden::make('created_by')
                            ->default(fn () => auth()->id()),
                        Forms\Components\TextInput::make('crop_type')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('hybrid_code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                    ])->columns(3),

                Forms\Components\Section::make('Parent Lines')
                    ->icon('heroicon-o-arrows-right-left')
                    ->schema([
                        Forms\Components\TextInput::make('parent_line_a')
                            ->label('Parent Line A')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('parent_line_b')
                            ->label('Parent Line B')
                            ->required()
                            ->maxLength(100),
                    ])->columns(2),

                Forms\Components\Section::make('Growth & Status')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Forms\Components\DatePicker::make('date_planted')
                            ->required(),
                        Forms\Components\Select::make('growth_status')
                            ->options(HybridizationRecord::GROWTH_STATUS_CHOICES)
                            ->required()
                            ->default('seedling'),
                        Forms\Components\Select::make('status')
                            ->options(HybridizationRecord::STATUS_CHOICES)
                            ->required()
                            ->default('draft')
                            ->disabled(fn () => auth()->user()?->isSupervisor()),
                    ])->columns(3),

                Forms\Components\Section::make('Notes & Remarks')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('admin_remarks')
                            ->label('Admin Remarks')
                            ->rows(3)
                            ->visible(fn () => !auth()->user()?->isSupervisor())
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Field Images')
                    ->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('field_images')
                            ->collection('field_images')
                            ->multiple()
                            ->image()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fieldSite.name')
                    ->label('Field Site')
                    ->sortable(),
                Tables\Columns\TextColumn::make('hybrid_code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('crop_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('parent_line_a')
                    ->label('Parent A')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('parent_line_b')
                    ->label('Parent B')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date_planted')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('growth_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'seedling' => 'gray',
                        'vegetative' => 'info',
                        'flowering' => 'warning',
                        'fruiting' => 'success',
                        'harvested' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'submitted' => 'warning',
                        'validated' => 'success',
                        'revision' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Created By')
                    ->toggleable(),
                self::getStatusColumn(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('field_site_id')
                    ->label('Field Site')
                    ->relationship('fieldSite', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options(HybridizationRecord::STATUS_CHOICES),
                Tables\Filters\SelectFilter::make('growth_status')
                    ->options(HybridizationRecord::GROWTH_STATUS_CHOICES),
                self::getStatusFilter(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                ...self::getApprovalActions(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->exporter(\App\Filament\Exports\HybridizationRecordExporter::class),
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
            'index' => Pages\ListHybridizationRecords::route('/'),
            'create' => Pages\CreateHybridizationRecord::route('/create'),
            'edit' => Pages\EditHybridizationRecord::route('/{record}/edit'),
        ];
    }
}
