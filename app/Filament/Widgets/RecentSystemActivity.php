<?php

namespace App\Filament\Widgets;

use App\Models\AuditLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentSystemActivity extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 2;
    protected static ?string $heading = 'Recent System Activity';

    public static function canView(): bool
    {
        return auth()->user()?->isSysAdmin();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AuditLog::whereIn('action', ['login', 'logout'])
                    ->latest()
                    ->limit(7)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('TIMESTAMP')
                    ->dateTime('M j, H:i'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('USER')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('action')
                    ->label('ACTION')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'login' => 'success',
                        'logout' => 'gray',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('DETAILS')
                    ->formatStateUsing(fn (string $state): string => "Ip: {$state}")
                    ->color('gray'),
            ])
            ->paginated(false)
            ->headerActions([
                Tables\Actions\Action::make('view_full_log')
                    ->label('View Full Log')
                    ->url(\App\Filament\Resources\AuditLogResource::getUrl('index'))
                    ->button()
                    ->outlined(),
            ]);
    }
}
