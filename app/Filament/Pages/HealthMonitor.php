<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;

class HealthMonitor extends Page
{
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-heart';

    protected string $view = 'filament.pages.health-monitor';

    protected static string|null|\UnitEnum $navigationGroup = 'Monitoring';

    protected static ?int $navigationSort = 1;

    public function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh All')
                ->icon('heroicon-o-arrow-path')
                ->action(fn () => $this->dispatch('$refresh'))
                ->color('gray'),

            Action::make('test_all_connections')
                ->label('Test All Connections')
                ->icon('heroicon-m-wifi')
                ->action(function (): void {
                    // This would trigger testing of all Redis connections
                    // Implementation would go here
                })
                ->color('info'),
        ];
    }
}
