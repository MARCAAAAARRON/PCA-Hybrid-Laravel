<?php

namespace App\Filament\Widgets;

use App\Models\HybridDistribution;
use App\Models\HybridizationRecord;
use App\Models\MonthlyHarvest;
use App\Models\NurseryOperation;
use App\Models\PollenProduction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return !auth()->user()?->isSysAdmin();
    }

    public function getHeading(): ?string
    {
        $user = auth()->user();
        if ($user?->isAdmin() || $user?->isSuperAdmin()) {
            return 'Field Data Stats';
        }
        return null;
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $isSupervisor = $user?->isSupervisor();
        $siteId = $user?->field_site_id;

        // Build queries with optional site scoping
        $harvestQuery = MonthlyHarvest::query();
        $nurseryQuery = NurseryOperation::query()->where('report_type', 'operation');
        $distributionQuery = HybridDistribution::query();
        $terminalQuery = NurseryOperation::query()->where('report_type', 'terminal');
        $pollenQuery = PollenProduction::query();
        $recordQuery = HybridizationRecord::query();
        $readyQuery = HybridizationRecord::query()->readyForHarvest();

        if ($isSupervisor && $siteId) {
            $distributionQuery->where('field_site_id', $siteId);
            $harvestQuery->where('field_site_id', $siteId);
            $nurseryQuery->where('field_site_id', $siteId);
            $terminalQuery->where('field_site_id', $siteId);
            $pollenQuery->where('field_site_id', $siteId);
            $recordQuery->where('field_site_id', $siteId);
            $readyQuery->where('field_site_id', $siteId);
        }

        $readyCount = $readyQuery->count();

        $stats = [
            Stat::make('Ready for Harvest', $readyCount)
                ->description($readyCount > 0 ? 'Need attention now' : 'All on track')
                ->icon('heroicon-o-calendar-days')
                ->color($readyCount > 0 ? 'danger' : 'success')
                ->extraAttributes(['class' => 'stat-gradient-6']),

            Stat::make('Hybrid Dist.', $distributionQuery->count())
                ->description('Farmer distribution records')
                ->icon('heroicon-o-truck')
                ->extraAttributes(['class' => 'stat-gradient-1']),

            Stat::make('Harvest', $harvestQuery->count())
                ->description('Seednut production records')
                ->icon('heroicon-o-academic-cap') // or tree if available. Let's use heroicon-o-list-bullet or tree? 
                // Actually, Filament uses Heroicons. Let's use heroicon-o-variable for now or check for tree.
                ->extraAttributes(['class' => 'stat-gradient-2']),

            Stat::make('Nursery', $nurseryQuery->count())
                ->description('Nursery reports')
                ->icon('heroicon-o-sun')
                ->extraAttributes(['class' => 'stat-gradient-3']),

            Stat::make('Terminal Rep.', $terminalQuery->count())
                ->description('Terminal reports')
                ->icon('heroicon-o-clipboard-document-check')
                ->extraAttributes(['class' => 'stat-gradient-4']),

            Stat::make('Pollen', $pollenQuery->count())
                ->description('Pollen production')
                ->icon('heroicon-o-beaker') // Standard for pollen/pollen extraction
                ->extraAttributes(['class' => 'stat-gradient-5']),
        ];

        return $stats;
    }
}
