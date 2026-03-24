<?php
namespace App\Filament\Resources\TerminalResource\Pages;

use App\Filament\Resources\TerminalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTerminalReports extends ListRecords
{
    protected static string $resource = TerminalResource::class;
    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
