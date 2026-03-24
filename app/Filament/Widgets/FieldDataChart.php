<?php

namespace App\Filament\Widgets;

use App\Models\HybridDistribution;
use App\Models\MonthlyHarvest;
use App\Models\NurseryOperation;
use App\Models\PollenProduction;
use Filament\Widgets\ChartWidget;

class FieldDataChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 2;

    public static function canView(): bool
    {
        return !auth()->user()?->isSysAdmin();
    }

    public function getHeading(): ?string
    {
        $user = auth()->user();
        if ($user?->isAdmin() || $user?->isSuperAdmin()) {
            return 'Field Data Overview (Combined)';
        }
        return 'Field Data Overview';
    }

    protected function getData(): array
    {
        $user = auth()->user();
        $isSupervisor = $user?->isSupervisor();
        $siteId = $user?->field_site_id;

        $harvestQuery = MonthlyHarvest::query();
        $nurseryQuery = NurseryOperation::query()->where('report_type', 'operation');
        $distributionQuery = HybridDistribution::query();
        $terminalQuery = NurseryOperation::query()->where('report_type', 'terminal');
        $pollenQuery = PollenProduction::query();

        if ($isSupervisor && $siteId) {
            $harvestQuery->where('field_site_id', $siteId);
            $nurseryQuery->where('field_site_id', $siteId);
            $distributionQuery->where('field_site_id', $siteId);
            $terminalQuery->where('field_site_id', $siteId);
            $pollenQuery->where('field_site_id', $siteId);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Records',
                    'data' => [
                        $harvestQuery->count(),
                        $nurseryQuery->count(),
                        $distributionQuery->count(),
                        $terminalQuery->count(),
                        $pollenQuery->count(),
                    ],
                    'backgroundColor' => [
                        '#c8e6c9', // Harvest (Green)
                        '#bbdefb', // Nursery (Blue)
                        '#e1bee7', // Distribution (Purple)
                        '#fed7aa', // Terminal (Orange)
                        '#ffecb3'  // Pollen (Yellow)
                    ],
                    'borderColor' => [
                        '#4caf50',
                        '#2196f3',
                        '#9c27b0',
                        '#ff9800',
                        '#f57f17'
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Harvest', 'Nursery', 'Distribution', 'Terminal Report', 'Pollen'],
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
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
