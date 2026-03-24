<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    public function getColumns(): int | string | array
    {
        return 3;
    }

    public function getTitle(): string|Htmlable
    {
        $user = auth()->user();
        if ($user?->isSysAdmin()) {
            return 'System Admin Dashboard';
        }
        if ($user?->isSuperAdmin()) {
            return 'Super Admin Dashboard';
        }
        if ($user?->isAdmin()) {
            return 'Admin Dashboard';
        }
        return 'Dashboard';
    }

    public function getSubheading(): string|Htmlable|null
    {
        $user = auth()->user();
        if ($user?->isSysAdmin()) {
            return 'User Governance and Audit Logs';
        }
        if ($user?->isSuperAdmin()) {
            return 'System control and governance';
        }
        if ($user?->isAdmin()) {
            return 'Multi-field overview and validation';
        }

        $site = $user->fieldSite?->name ?? 'All Field Sites';
        return new \Illuminate\Support\HtmlString("
            <div class='flex items-center gap-1 text-gray-500 dark:text-gray-400'>
                <x-heroicon-m-map-pin class='h-4 w-4' />
                <span>{$site}</span>
            </div>
        ");
    }


    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        if ($user?->isAdmin() || $user?->isSuperAdmin()) {
            return [
                Action::make('reports')
                    ->label('Reports')
                    ->color('primary')
                    ->icon('heroicon-m-document-chart-bar')
                    ->url(\App\Filament\Resources\ReportResource::getUrl('index')),
            ];
        }

        return [
            Action::make('addHarvest')
                ->label('Add Harvest')
                ->color('primary')
                ->icon('heroicon-m-plus')
                ->url(\App\Filament\Resources\MonthlyHarvestResource::getUrl('create')),
                
            Action::make('addNursery')
                ->label('Add Nursery')
                ->color('primary')
                ->icon('heroicon-m-plus')
                ->url(\App\Filament\Resources\NurseryOperationResource::getUrl('create')),
                
            Action::make('addDistribution')
                ->label('Add Distribution')
                ->color('primary')
                ->icon('heroicon-m-plus')
                ->url(\App\Filament\Resources\HybridDistributionResource::getUrl('create')),
                
            Action::make('addPollen')
                ->label('Add Pollen')
                ->color('primary')
                ->icon('heroicon-m-plus')
                ->url(\App\Filament\Resources\PollenProductionResource::getUrl('create')),
        ];
    }
}
