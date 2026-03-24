<?php
namespace App\Filament\Resources\NurseryOperationResource\Pages;
use App\Filament\Resources\NurseryOperationResource;
use Filament\Resources\Pages\ListRecords;
class ListNurseryOperations extends ListRecords
{
    protected static string $resource = NurseryOperationResource::class;
    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
