<?php
namespace App\Filament\Resources\HybridizationRecordResource\Pages;
use App\Filament\Resources\HybridizationRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditHybridizationRecord extends EditRecord
{
    protected static string $resource = HybridizationRecordResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
