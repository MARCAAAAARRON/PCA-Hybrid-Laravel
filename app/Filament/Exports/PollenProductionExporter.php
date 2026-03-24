<?php

namespace App\Filament\Exports;

use App\Models\PollenProduction;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PollenProductionExporter extends Exporter
{
    protected static ?string $model = PollenProduction::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('fieldSite.name')->label('Field Site'),
            ExportColumn::make('report_month')->label('Report Month'),
            ExportColumn::make('month_label')->label('Month'),
            ExportColumn::make('pollen_variety')->label('Pollen Variety'),
            ExportColumn::make('ending_balance_prev')->label('Ending Balance (Prev Month)'),
            ExportColumn::make('pollen_source')->label('Source'),
            ExportColumn::make('date_received')->label('Date Received'),
            ExportColumn::make('pollens_received')->label('Pollens Received'),
            ExportColumn::make('week1')->label('Week 1'),
            ExportColumn::make('week2')->label('Week 2'),
            ExportColumn::make('week3')->label('Week 3'),
            ExportColumn::make('week4')->label('Week 4'),
            ExportColumn::make('week5')->label('Week 5'),
            ExportColumn::make('total_utilization')->label('Total Utilization'),
            ExportColumn::make('ending_balance')->label('Ending Balance'),
            ExportColumn::make('remarks'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your pollen production export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
