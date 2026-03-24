<?php

namespace App\Filament\Widgets;

use App\Models\FieldSite;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SysAdminStats extends BaseWidget
{
    protected static ?int $sort = 0;

    public static function canView(): bool
    {
        return auth()->user()?->isSysAdmin();
    }

    protected function getStats(): array
    {
        $totalUsers = User::count();
        $activeUsers = User::whereNotNull('email_verified_at')->count();
        $totalSites = FieldSite::count();

        return [
            Stat::make('Total Users', $totalUsers)
                ->icon('heroicon-o-users')
                ->color('success')
                ->extraAttributes(['class' => 'stat-gradient-2']), // Greenish
                
            Stat::make('Active Users', $activeUsers)
                ->icon('heroicon-o-user-group')
                ->color('info')
                ->extraAttributes(['class' => 'stat-gradient-3']), // Blueish
                
            Stat::make('Field Sites', $totalSites)
                ->icon('heroicon-o-map-pin')
                ->color('danger')
                ->extraAttributes(['class' => 'stat-gradient-1']), // Pinkish
        ];
    }
}
