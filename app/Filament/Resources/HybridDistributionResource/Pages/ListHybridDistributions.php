<?php
namespace App\Filament\Resources\HybridDistributionResource\Pages;
use App\Filament\Resources\HybridDistributionResource;
use Filament\Resources\Pages\ListRecords;
class ListHybridDistributions extends ListRecords
{
    protected static string $resource = HybridDistributionResource::class;
    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
