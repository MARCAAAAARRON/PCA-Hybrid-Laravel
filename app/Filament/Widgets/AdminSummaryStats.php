<?php

namespace App\Filament\Widgets;

use App\Models\FieldSite;
use App\Models\HybridizationRecord;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminSummaryStats extends BaseWidget
{
    protected static ?int $sort = 0; // Top of the page

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() || auth()->user()?->isSuperAdmin();
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $totalRecords = HybridizationRecord::count();
        
        if ($user?->isSuperAdmin()) {
            return [
                Stat::make('Total Records', $totalRecords)
                    ->icon('heroicon-o-document-duplicate')
                    ->extraAttributes(['class' => 'stat-gradient-4']), // Yellow ish from screenshot
                    
                Stat::make('Field Sites', FieldSite::count())
                    ->icon('heroicon-o-map-pin')
                    ->extraAttributes(['class' => 'stat-gradient-1']), // Pinkish from screenshot
            ];
        }

        $pendingValidation = HybridizationRecord::where('status', 'submitted')->count();
        $sites = FieldSite::limit(2)->get();
        
        $stats = [
            Stat::make('Total Records', $totalRecords)
                ->description('Across all field sites')
                ->icon('heroicon-o-document-duplicate')
                ->color('success')
                ->extraAttributes(['class' => 'stat-gradient-2']), // Greenish
                
            Stat::make('Pending Validation', $pendingValidation)
                ->description('Records awaiting approval')
                ->icon('heroicon-o-exclamation-circle')
                ->color('warning')
                ->extraAttributes(['class' => 'stat-gradient-4']), // Orangish/Yellowish
        ];
        
        foreach ($sites as $index => $site) {
            $gradientClass = ($index % 2 == 0) ? 'stat-gradient-3' : 'stat-gradient-1'; // Blue vs Pinkish
            $stats[] = Stat::make($site->name ?? 'Field Site', HybridizationRecord::where('field_site_id', $site->id)->count())
                ->description('Total records')
                ->icon('heroicon-o-map-pin')
                ->extraAttributes(['class' => $gradientClass]);
        }

        return $stats;
    }
}
