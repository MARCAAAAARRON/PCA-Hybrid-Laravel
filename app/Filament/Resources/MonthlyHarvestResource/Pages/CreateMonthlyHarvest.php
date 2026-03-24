<?php
namespace App\Filament\Resources\MonthlyHarvestResource\Pages;
use App\Filament\Resources\MonthlyHarvestResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Traits\HasCarryForward;
class CreateMonthlyHarvest extends CreateRecord
{
    use HasCarryForward;

    protected static string $resource = MonthlyHarvestResource::class;

    protected array $carryForwardFields = [
        'location',
        'farm_name',
        'area_ha',
        'age_of_palms',
        'num_hybridized_palms',
        'varieties' => ['variety', 'seednuts_type', 'seednuts_count', 'remarks']
    ];

    public function mount(): void
    {
        parent::mount();

        if (auth()->user()?->isSupervisor()) {
            $this->loadLatestRecordData();
        }
    }
}
