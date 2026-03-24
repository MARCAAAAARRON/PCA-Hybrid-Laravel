<?php

namespace App\Filament\Resources\HybridDistributionResource\Pages;

use App\Filament\Resources\HybridDistributionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHybridDistribution extends EditRecord
{
    protected static string $resource = HybridDistributionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    /**
     * Pre-fill the virtual 'gender' field from is_male/is_female booleans.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($data['is_male'] ?? false) {
            $data['gender'] = 'M';
        } elseif ($data['is_female'] ?? false) {
            $data['gender'] = 'F';
        }

        return $data;
    }

    /**
     * Map the virtual 'gender' field back to is_male/is_female on save.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $gender = $data['gender'] ?? null;
        unset($data['gender']);
        $data['is_male'] = $gender === 'M';
        $data['is_female'] = $gender === 'F';

        return $data;
    }
}
