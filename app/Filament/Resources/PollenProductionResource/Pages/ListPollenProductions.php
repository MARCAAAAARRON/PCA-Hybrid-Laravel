<?php
namespace App\Filament\Resources\PollenProductionResource\Pages;
use App\Filament\Resources\PollenProductionResource;
use Filament\Resources\Pages\ListRecords;
class ListPollenProductions extends ListRecords
{
    protected static string $resource = PollenProductionResource::class;
    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // PollenProductionResource\Widgets\PollenStockWidget::class,
        ];
    }
}
