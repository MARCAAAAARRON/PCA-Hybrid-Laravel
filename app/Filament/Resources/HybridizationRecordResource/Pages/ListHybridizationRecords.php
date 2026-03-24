<?php
namespace App\Filament\Resources\HybridizationRecordResource\Pages;
use App\Filament\Resources\HybridizationRecordResource;
use Filament\Resources\Pages\ListRecords;
class ListHybridizationRecords extends ListRecords
{
    protected static string $resource = HybridizationRecordResource::class;
    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
