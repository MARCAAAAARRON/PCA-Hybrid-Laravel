<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class DashboardActionCardsWidget extends Widget
{
    protected static string $view = 'filament.widgets.dashboard-action-cards';

    protected static ?int $sort = -9;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return !auth()->user()?->isSuperAdmin();
    }
}
