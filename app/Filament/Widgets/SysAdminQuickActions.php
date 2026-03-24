<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class SysAdminQuickActions extends Widget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 1;
    protected static string $view = 'filament.widgets.sys-admin-quick-actions';

    public static function canView(): bool
    {
        return auth()->user()?->isSysAdmin();
    }
}
