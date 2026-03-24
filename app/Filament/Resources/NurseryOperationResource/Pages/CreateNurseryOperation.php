<?php
namespace App\Filament\Resources\NurseryOperationResource\Pages;
use App\Filament\Resources\NurseryOperationResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Traits\HasCarryForward;
class CreateNurseryOperation extends CreateRecord
{
    use HasCarryForward;

    protected static string $resource = NurseryOperationResource::class;

    protected string $carryForwardType = 'operation';

    protected array $carryForwardFields = [
        'region_province_district',
        'barangay_municipality',
        'proponent_entity',
        'proponent_representative',
        'target_seednuts',
        'batches' => [
            'seednuts_harvested',
            'date_harvested',
            'date_received',
            'source_of_seednuts',
            'varieties' => [
                'variety', 'seednuts_sown', 'date_sown', 'seedlings_germinated', 
                'ungerminated_seednuts', 'culled_seedlings', 'good_seedlings', 
                'ready_to_plant', 'seedlings_dispatched', 'remarks'
            ]
        ]
    ];

    public function mount(): void
    {
        parent::mount();

        if (auth()->user()?->isSupervisor()) {
            $this->loadLatestRecordData();
        }
    }
}
