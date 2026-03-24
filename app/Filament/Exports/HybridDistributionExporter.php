<?php

namespace App\Filament\Exports;

use App\Models\HybridDistribution;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class HybridDistributionExporter extends Exporter
{
    protected static ?string $model = HybridDistribution::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('fieldSite.name')->label('Field Site'),
            ExportColumn::make('report_month')->label('Report Month'),
            ExportColumn::make('region'),
            ExportColumn::make('province'),
            ExportColumn::make('district'),
            ExportColumn::make('municipality'),
            ExportColumn::make('barangay'),
            ExportColumn::make('farmer_last_name')->label('Family Name'),
            ExportColumn::make('farmer_first_name')->label('Given Name'),
            ExportColumn::make('farmer_middle_initial')->label('M.I.'),
            ExportColumn::make('is_male')->label('Male'),
            ExportColumn::make('is_female')->label('Female'),
            ExportColumn::make('farm_barangay')->label('Farm Barangay'),
            ExportColumn::make('farm_municipality')->label('Farm Municipality'),
            ExportColumn::make('farm_province')->label('Farm Province'),
            ExportColumn::make('seedlings_received')->label('No. of Seedlings Received'),
            ExportColumn::make('date_received')->label('Date Received'),
            ExportColumn::make('variety')->label('Type/Variety'),
            ExportColumn::make('seedlings_planted')->label('No. of Seedlings Planted'),
            ExportColumn::make('date_planted')->label('Date Planted'),
            ExportColumn::make('remarks'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your hybrid distribution export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
