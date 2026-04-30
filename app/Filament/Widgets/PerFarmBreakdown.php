<?php

namespace App\Filament\Widgets;

use App\Models\FieldSite;
use App\Models\HybridDistribution;
use App\Models\MonthlyHarvest;
use App\Models\NurseryOperation;
use App\Models\PollenProduction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class PerFarmBreakdown extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Per-Farm Breakdown';

    public static function canView(): bool
    {
        return auth()->user()?->isManager() || auth()->user()?->isAdmin();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(FieldSite::query())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('FARM / FIELD SITE')
                    ->icon('heroicon-m-map-pin')
                    ->iconColor('primary')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('harvest_count')
                    ->label('HARVEST')
                    ->getStateUsing(fn($record) => MonthlyHarvest::where('field_site_id', $record->id)->count())
                    ->alignment('center'),
                Tables\Columns\TextColumn::make('nursery_count')
                    ->label('NURSERY')
                    ->getStateUsing(fn($record) => NurseryOperation::where('field_site_id', $record->id)->where('report_type', 'operation')->count())
                    ->alignment('center'),
                Tables\Columns\TextColumn::make('terminal_count')
                    ->label('TERMINAL')
                    ->getStateUsing(fn($record) => NurseryOperation::where('field_site_id', $record->id)->where('report_type', 'terminal')->count())
                    ->alignment('center'),
                Tables\Columns\TextColumn::make('pollen_count')
                    ->label('POLLEN')
                    ->getStateUsing(fn($record) => PollenProduction::where('field_site_id', $record->id)->count())
                    ->alignment('center'),
                Tables\Columns\TextColumn::make('distribution_count')
                    ->label('DISTRIBUTION')
                    ->getStateUsing(fn($record) => HybridDistribution::where('field_site_id', $record->id)->count())
                    ->alignment('center'),
                Tables\Columns\TextColumn::make('total_count')
                    ->label('TOTAL')
                    ->weight('bold')
                    ->getStateUsing(fn($record) => 
                        MonthlyHarvest::where('field_site_id', $record->id)->count() +
                        NurseryOperation::where('field_site_id', $record->id)->count() +
                        PollenProduction::where('field_site_id', $record->id)->count() +
                        HybridDistribution::where('field_site_id', $record->id)->count()
                    )
                    ->alignment('center'),
            ])
            ->paginated(false);
    }
}
