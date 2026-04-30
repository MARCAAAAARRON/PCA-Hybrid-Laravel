<?php

namespace App\Filament\Widgets;

use App\Models\HarvestVariety;
use App\Models\HybridDistribution;
use App\Models\PollenProduction;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

class OperationsFunnelChart extends ChartWidget
{
    protected static ?string $heading = '📊 Regional Operations Funnel';
    
    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = ['default' => 'full', 'lg' => 2];
    protected static ?string $maxHeight = '250px';

    public static function canView(): bool
    {
        return auth()->user()?->isManager() || auth()->user()?->isAdmin();
    }

    protected function getData(): array
    {
        // 1. Pollen Utilization (grams)
        $pollenUtilized = PollenProduction::withoutGlobalScopes()->sum('total_utilization');

        // 2. Seednuts Harvested (count)
        $seednutsHarvested = HarvestVariety::whereHas('monthlyHarvest', function ($q) {
            $q->withoutGlobalScopes();
        })->sum('seednuts_count');

        // 3. Seedlings Distributed (count)
        $seedlingsDistributed = HybridDistribution::withoutGlobalScopes()->sum('seedlings_planted');

        return [
            'datasets' => [
                [
                    'label' => 'Total Count',
                    'data' => [$pollenUtilized, $seednutsHarvested, $seedlingsDistributed],
                    'backgroundColor' => [
                        'rgba(245, 158, 11, 0.8)', // Amber (Pollen)
                        'rgba(22, 163, 74, 0.8)',  // Green (Seednuts)
                        'rgba(14, 165, 233, 0.8)', // Sky (Seedlings)
                    ],
                    'borderColor' => [
                        '#f59e0b',
                        '#16a34a',
                        '#0ea5e9',
                    ],
                    'borderWidth' => 2,
                    'borderRadius' => 6,
                ],
            ],
            'labels' => ['Pollen Used (g)', 'Seednuts Harvested', 'Seedlings Distributed'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
                'tooltip' => [
                    'callbacks' => [
                        // Removing the custom label function as it's not strictly necessary and can cause issues if not formatted perfectly for Chart.js
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => ['display' => true, 'color' => 'rgba(0,0,0,0.05)'],
                ],
                'x' => [
                    'grid' => ['display' => false],
                ],
            ],
        ];
    }
}
