<?php
namespace App\Filament\Resources\PollenProductionResource\Pages;
use App\Filament\Resources\PollenProductionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditPollenProduction extends EditRecord
{
    protected static string $resource = PollenProductionResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
