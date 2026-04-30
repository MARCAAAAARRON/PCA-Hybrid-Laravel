<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuditLogResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Audit Logs';

    protected static ?int $navigationSort = 103;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any'];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable(),
                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'login' => 'info',
                        'logout' => 'gray',
                        'create' => 'success',
                        'update' => 'warning',
                        'delete' => 'danger',
                        'submit' => 'primary',
                        'validate' => 'success',
                        'revision' => 'danger',
                        'report' => 'info',
                        'user_mgmt' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => AuditLog::ACTION_CHOICES[$state] ?? $state),
                Tables\Columns\TextColumn::make('model_name')
                    ->label('Model')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('object_id')
                    ->label('Record ID')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('formatted_details')
                    ->label('Details')
                    ->limit(60)
                    ->wrap(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->options(AuditLog::ACTION_CHOICES),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Audit Log Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Timestamp')
                            ->dateTime('Y-m-d H:i:s'),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('User'),
                        Infolists\Components\TextEntry::make('action')
                            ->badge(),
                        Infolists\Components\TextEntry::make('model_name')
                            ->label('Model'),
                        Infolists\Components\TextEntry::make('object_id')
                            ->label('Record ID'),
                        Infolists\Components\TextEntry::make('ip_address')
                            ->label('IP Address'),
                        Infolists\Components\TextEntry::make('formatted_details')
                            ->label('Details')
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }
}
