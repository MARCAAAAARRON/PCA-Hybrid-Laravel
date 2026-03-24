<?php

namespace App\Filament\Exports;

use App\Models\HybridizationRecord;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class HybridizationRecordExporter extends Exporter
{
    protected static ?string $model = HybridizationRecord::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('fieldSite.name')->label('Field Site'),
            ExportColumn::make('hybrid_code')->label('Hybrid Code'),
            ExportColumn::make('crop_type')->label('Crop Type'),
            ExportColumn::make('parent_line_a')->label('Parent Line A'),
            ExportColumn::make('parent_line_b')->label('Parent Line B'),
            ExportColumn::make('date_planted')->label('Date Planted'),
            ExportColumn::make('growth_status')->label('Growth Status'),
            ExportColumn::make('status')->label('Record Status'),
            ExportColumn::make('notes'),
            ExportColumn::make('admin_remarks')->label('Admin Remarks'),
            ExportColumn::make('creator.name')->label('Created By'),
            ExportColumn::make('created_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your hybridization record export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
