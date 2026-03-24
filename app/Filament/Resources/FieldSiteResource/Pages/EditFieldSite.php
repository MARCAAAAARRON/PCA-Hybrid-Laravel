<?php

namespace App\Filament\Resources\FieldSiteResource\Pages;

use App\Filament\Resources\FieldSiteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFieldSite extends EditRecord
{
    protected static string $resource = FieldSiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
