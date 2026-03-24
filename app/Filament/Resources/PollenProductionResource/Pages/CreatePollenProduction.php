<?php
namespace App\Filament\Resources\PollenProductionResource\Pages;
use App\Filament\Resources\PollenProductionResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Traits\HasCarryForward;
class CreatePollenProduction extends CreateRecord
{
    use HasCarryForward;

    protected static string $resource = PollenProductionResource::class;

    protected array $carryForwardFields = [
        'pollen_variety',
        'ending_balance_prev' // Map ending_balance to this in trait
    ];

    public function mount(): void
    {
        parent::mount();

        if (auth()->user()?->isSupervisor()) {
            $this->loadLatestRecordData();
        }
    }
}
