<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FieldSiteResource\Pages;
use App\Models\FieldSite;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class FieldSiteResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = FieldSite::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 100;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Field Site Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Users')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                // ─── QR Code Actions ────────────────────────────
                Tables\Actions\Action::make('showQrCode')
                    ->label('QR Code')
                    ->icon('heroicon-m-qr-code')
                    ->color('success')
                    ->modalHeading(fn (FieldSite $record) => "QR Code — {$record->name}")
                    ->modalDescription('Scan this QR code with a phone camera to quickly open the Monthly Harvest form for this site.')
                    ->modalContent(function (FieldSite $record) {
                        $url = url("/site/{$record->id}/quick-add");
                        $encodedUrl = urlencode($url);
                        $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={$encodedUrl}&color=0f172a&bgcolor=ffffff&margin=10";
                        $printUrl = route('site.qr', $record);
                        return new HtmlString("
                            <div class=\"flex flex-col items-center gap-4 py-4\">
                                <div class=\"rounded-xl border-2 border-gray-200 p-3 bg-white\">
                                    <img src=\"{$qrImageUrl}\" alt=\"QR Code for {$record->name}\" width=\"220\" height=\"220\" style=\"display:block;\" />
                                </div>
                                <p class=\"text-xs text-gray-400 font-mono break-all text-center\">{$url}</p>
                                <a href=\"{$printUrl}\"
                                   target=\"_blank\"
                                   class=\"inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 transition\">
                                    <svg class=\"h-4 w-4\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\" stroke-width=\"2\">
                                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18.75 12h.008v.008h-.008V12zm-2.25 0h.008v.008H16.5V12z\"/>
                                    </svg>
                                    Open Printable Version
                                </a>
                            </div>
                        ");
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFieldSites::route('/'),
            'create' => Pages\CreateFieldSite::route('/create'),
            'view' => Pages\ViewFieldSite::route('/{record}'),
            'edit' => Pages\EditFieldSite::route('/{record}/edit'),
        ];
    }
}
