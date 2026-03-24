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
}
