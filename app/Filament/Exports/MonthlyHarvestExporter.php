<?php

namespace App\Filament\Exports;

use App\Models\MonthlyHarvest;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class MonthlyHarvestExporter extends Exporter
{
    protected static ?string $model = MonthlyHarvest::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('fieldSite.name')->label('Field Site'),
            ExportColumn::make('report_month')->label('Report Month'),
            ExportColumn::make('location')->label('Farm Location'),
            ExportColumn::make('farm_name')->label('Name of Partner/Farm'),
            ExportColumn::make('area_ha')->label('Area (Ha.)'),
            ExportColumn::make('age_of_palms')->label('Age of Palms'),
            ExportColumn::make('num_hybridized_palms')->label('No. of Hybridized Palms'),
            ExportColumn::make('variety')->label('Variety/Hybrid Crosses'),
            ExportColumn::make('seednuts_produced')->label('Seednuts Produced'),
            ExportColumn::make('production_jan')->label('Jan'),
            ExportColumn::make('production_feb')->label('Feb'),
            ExportColumn::make('production_mar')->label('Mar'),
            ExportColumn::make('production_apr')->label('Apr'),
            ExportColumn::make('production_may')->label('May'),
            ExportColumn::make('production_jun')->label('Jun'),
            ExportColumn::make('production_jul')->label('Jul'),
            ExportColumn::make('production_aug')->label('Aug'),
            ExportColumn::make('production_sep')->label('Sep'),
            ExportColumn::make('production_oct')->label('Oct'),
            ExportColumn::make('production_nov')->label('Nov'),
            ExportColumn::make('production_dec')->label('Dec'),
            ExportColumn::make('remarks'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your monthly harvest export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
