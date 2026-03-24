<?php
namespace App\Filament\Resources\NurseryOperationResource\Pages;
use App\Filament\Resources\NurseryOperationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditNurseryOperation extends EditRecord
{
    protected static string $resource = NurseryOperationResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
