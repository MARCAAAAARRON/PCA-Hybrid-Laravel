<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    public function getColumns(): int | string | array
    {
        return 1;
    }

    public function getTitle(): string|Htmlable
    {
        return 'Dashboard';
    }

    public function getSubheading(): string|Htmlable|null
    {
        // Removed subheading to favor our DashboardWelcomeWidget
        return null;
    }

    protected function getHeaderActions(): array
    {
        // Removed header actions since we now feature highly visible "Quick Links" cards in the UI
        return [];
    }
}
