<?php

namespace App\Filament\Exports;

use App\Models\NurseryOperation;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class NurseryOperationExporter extends Exporter
{
    protected static ?string $model = NurseryOperation::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('fieldSite.name')->label('Field Site'),
            ExportColumn::make('report_month')->label('Report Month'),
            ExportColumn::make('report_type')->label('Report Type'),
            ExportColumn::make('region_province_district')->label('Region/Province/District'),
            ExportColumn::make('barangay_municipality')->label('Barangay/Municipality'),
            ExportColumn::make('proponent_entity')->label('Proponent Entity'),
            ExportColumn::make('proponent_representative')->label('Representative'),
            ExportColumn::make('target_seednuts')->label('Target Seednuts'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your nursery operation export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
