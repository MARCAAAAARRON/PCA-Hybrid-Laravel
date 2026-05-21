<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Livewire\Attributes\On;

class DashboardWelcomeWidget extends Widget
{
    protected static string $view = 'filament.widgets.dashboard-welcome';

    protected static ?int $sort = -10;
    protected int | string | array $columnSpan = 'full';

    public ?int $year = null;

    public function mount(): void
    {
        $this->year = (int) now()->year;
    }

    /**
     * When the year dropdown changes, dispatch to all sibling widgets.
     */
    public function updatedYear($value): void
    {
        $this->year = (int) $value;
        $this->dispatch('dashboard-year-changed', year: $this->year);
    }

    protected function getViewData(): array
    {
        $user = auth()->user();
        return [
            'user' => $user,
            'year' => $this->year ?? (int) now()->year,
            'yearOptions' => collect(range(now()->year, 2024, -1))
                ->mapWithKeys(fn ($y) => [$y => $y])
                ->toArray(),
        ];
    }
}
