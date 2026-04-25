<?php
namespace App\Filament\Resources\MonthlyHarvestResource\Pages;
use App\Filament\Resources\MonthlyHarvestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditMonthlyHarvest extends EditRecord
{
    protected static string $resource = MonthlyHarvestResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $palms = (int) ($data['num_hybridized_palms'] ?? 0);
        $totalSeednuts = 0;
        
        if (!empty($data['varieties'])) {
            foreach ($data['varieties'] as $variety) {
                $totalSeednuts += (int) ($variety['seednuts_count'] ?? 0);
            }
        }
        
        // Logical Validation: Max ~30 seednuts per palm per month (historical anomaly threshold)
        $maxExpected = $palms * 30; 
        
        if ($palms > 0 && $totalSeednuts > $maxExpected) {
            \Filament\Notifications\Notification::make()
                ->danger()
                ->title('Data Anomaly Detected')
                ->body("Total seednuts ({$totalSeednuts}) is abnormally high for {$palms} palms. The maximum expected is {$maxExpected}. Please verify your inputs.")
                ->persistent()
                ->send();
                
            throw new \Filament\Support\Exceptions\Halt();
        }
        
        return $data;
    }
}
