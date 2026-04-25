<?php

namespace App\Filament\Widgets;

use App\Models\HarvestVariety;
use App\Models\HybridDistribution;
use App\Models\PollenProduction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EfficiencyStatsWidget extends BaseWidget
{
    protected static ?int $sort = 9;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() || auth()->user()?->isSuperAdmin();
    }

    protected function getStats(): array
    {
        // Total Pollen Utilized (grams)
        $totalPollen = PollenProduction::withoutGlobalScopes()->sum('total_utilization');

        // Total Seednuts Harvested
        $totalSeednuts = HarvestVariety::whereHas('monthlyHarvest', function ($q) {
            $q->withoutGlobalScopes();
        })->sum('seednuts_count');

        // Total Seedlings Distributed (successfully reached farmers)
        $totalSeedlings = HybridDistribution::withoutGlobalScopes()->sum('seedlings_planted');

        // Calculations
        $pollenEfficiency = $totalPollen > 0 ? round($totalSeednuts / $totalPollen, 1) : 0;
        
        $survivalRate = 0;
        if ($totalSeednuts > 0) {
            $survivalRate = round(($totalSeedlings / $totalSeednuts) * 100, 1);
        }

        return [
            Stat::make('Pollen Efficiency', $pollenEfficiency)
                ->description('Seednuts produced per gram of pollen')
                ->descriptionIcon('heroicon-m-bolt')
                ->color($pollenEfficiency >= 10 ? 'success' : 'warning')
                ->chart([7, 8, 9, 10, $pollenEfficiency]), // Mock chart trend

            Stat::make('Seedling Survival Rate', $survivalRate . '%')
                ->description('From harvest to distribution')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color($survivalRate >= 80 ? 'success' : ($survivalRate >= 50 ? 'warning' : 'danger'))
                ->chart([60, 70, 75, 80, $survivalRate]), // Mock chart trend
                
            Stat::make('Total Loss Pipeline', number_format($totalSeednuts - $totalSeedlings))
                ->description('Seednuts that didn\'t reach distribution')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
        ];
    }
}
