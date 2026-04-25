<?php

namespace App\Filament\Resources\FieldSiteResource\Pages;

use App\Filament\Resources\FieldSiteResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\HtmlString;

class ViewFieldSite extends ViewRecord
{
    protected static string $resource = FieldSiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\Action::make('printQr')
                ->label('Print QR Code')
                ->icon('heroicon-m-qr-code')
                ->color('success')
                ->url(fn () => route('site.qr', $this->record))
                ->openUrlInNewTab(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Site Information')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Site Name')
                            ->weight('bold')
                            ->size('lg'),
                        Infolists\Components\TextEntry::make('description')
                            ->label('Description')
                            ->default('—'),
                        Infolists\Components\TextEntry::make('users_count')
                            ->label('Assigned Users')
                            ->getStateUsing(fn ($record) => $record->users()->count()),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                    ])->columns(2),

                Infolists\Components\Section::make('QR Code — Quick Add')
                    ->icon('heroicon-o-qr-code')
                    ->description('Scan this QR code on a field marker to instantly open the Monthly Harvest form for this site.')
                    ->schema([
                        Infolists\Components\ViewEntry::make('qr_code')
                            ->label('')
                            ->view('filament.infolists.qr-code-entry')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
