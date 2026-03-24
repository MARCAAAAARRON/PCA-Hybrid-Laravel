<?php
namespace App\Filament\Resources\MonthlyHarvestResource\Pages;
use App\Filament\Resources\MonthlyHarvestResource;
use Filament\Resources\Pages\ListRecords;
class ListMonthlyHarvests extends ListRecords
{
    protected static string $resource = MonthlyHarvestResource::class;
    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
