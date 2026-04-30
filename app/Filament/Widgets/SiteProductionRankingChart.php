<?php

namespace App\Filament\Widgets;

use App\Models\FieldSite;
use App\Models\HarvestVariety;
use App\Models\MonthlyHarvest;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SiteProductionRankingChart extends ChartWidget
{
    protected static ?string $heading = '🏆 Site Production Ranking';

    protected static ?int $sort = 7;
    protected int | string | array $columnSpan = ['default' => 'full', 'lg' => 1];
    protected static ?string $maxHeight = '200px';

    public static function canView(): bool
    {
        return auth()->user()?->isManager() || auth()->user()?->isAdmin();
    }

    protected function getData(): array
    {
        // Get total seednuts per site from harvest_varieties
        $sites = FieldSite::all();
        $labels = [];
        $data = [];
        $colors = [];

        $palette = [
            ['bg' => 'rgba(22, 163, 74, 0.7)', 'border' => '#16a34a'],   // green
            ['bg' => 'rgba(37, 99, 235, 0.7)', 'border' => '#2563eb'],   // blue
            ['bg' => 'rgba(234, 88, 12, 0.7)', 'border' => '#ea580c'],   // orange
            ['bg' => 'rgba(147, 51, 234, 0.7)', 'border' => '#9333ea'],  // purple
            ['bg' => 'rgba(220, 38, 38, 0.7)', 'border' => '#dc2626'],   // red
            ['bg' => 'rgba(14, 165, 233, 0.7)', 'border' => '#0ea5e9'],  // sky
            ['bg' => 'rgba(245, 158, 11, 0.7)', 'border' => '#f59e0b'],  // amber
        ];

        $siteData = [];
        foreach ($sites as $site) {
            $total = HarvestVariety::whereHas('monthlyHarvest', function ($q) use ($site) {
                $q->withoutGlobalScopes()->where('field_site_id', $site->id);
            })->sum('seednuts_count');

            $siteData[] = ['name' => $site->name, 'total' => $total];
        }

        // Sort by production descending
        usort($siteData, fn($a, $b) => $b['total'] - $a['total']);

        $bgColors = [];
        $borderColors = [];
        foreach ($siteData as $i => $s) {
            $labels[] = $s['name'];
            $data[] = $s['total'];
            $p = $palette[$i % count($palette)];
            $bgColors[] = $p['bg'];
            $borderColors[] = $p['border'];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Seednuts',
                    'data' => $data,
                    'backgroundColor' => $bgColors,
                    'borderColor' => $borderColors,
                    'borderWidth' => 2,
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'ticks' => ['precision' => 0],
                    'grid' => ['display' => true, 'color' => 'rgba(0,0,0,0.05)'],
                ],
                'y' => [
                    'grid' => ['display' => false],
                ],
            ],
        ];
    }
}
