<?php

namespace App\Filament\Resources\RedisConnections\Tables;

use App\Models\RedisConnection;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class RedisConnectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('host')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('port')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                BadgeColumn::make('health_status')
                    ->label('Health')
                    ->colors([
                        'success' => 'healthy',
                        'danger' => 'unhealthy',
                        'warning' => ['error', 'timeout'],
                        'gray' => null,
                    ])
                    ->icons([
                        'heroicon-m-check-circle' => 'healthy',
                        'heroicon-m-x-circle' => 'unhealthy',
                        'heroicon-m-exclamation-triangle' => ['error', 'timeout'],
                    ]),

                TextColumn::make('environment')
                    ->badge()
                    ->color('info'),

                TextColumn::make('region')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('applications_count')
                    ->label('Applications')
                    ->counts('applications')
                    ->sortable(),

                TextColumn::make('last_health_check_at')
                    ->label('Last Check')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->boolean()
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->native(false),

                SelectFilter::make('health_status')
                    ->label('Health Status')
                    ->options([
                        'healthy' => 'Healthy',
                        'unhealthy' => 'Unhealthy',
                        'error' => 'Error',
                        'timeout' => 'Timeout',
                    ]),

                SelectFilter::make('environment')
                    ->label('Environment'),

                SelectFilter::make('provider')
                    ->label('Provider'),
            ])
            ->recordActions([
                Action::make('test_connection')
                    ->label('Test')
                    ->icon('heroicon-m-wifi')
                    ->color('info')
                    ->action(function (RedisConnection $record): void {
                        $success = $record->testConnection();

                        if ($success) {
                            Notification::make()
                                ->title('Connection Test Successful')
                                ->body("Successfully connected to {$record->name}")
                                ->success()
                                ->send();

                            // Update health status
                            $record->update([
                                'health_status' => 'healthy',
                                'last_health_check_at' => now(),
                                'last_error' => null,
                            ]);
                        } else {
                            Notification::make()
                                ->title('Connection Test Failed')
                                ->body("Failed to connect to {$record->name}: {$record->last_error}")
                                ->danger()
                                ->send();
                        }
                    }),

                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('test_connections')
                        ->label('Test Selected Connections')
                        ->icon('heroicon-m-wifi')
                        ->color('info')
                        ->action(function (Collection $records): void {
                            $successCount = 0;
                            $totalCount = $records->count();

                            foreach ($records as $record) {
                                if ($record->testConnection()) {
                                    $record->update([
                                        'health_status' => 'healthy',
                                        'last_health_check_at' => now(),
                                        'last_error' => null,
                                    ]);
                                    $successCount++;
                                }
                            }

                            Notification::make()
                                ->title('Bulk Connection Test Complete')
                                ->body("Successfully tested {$successCount} of {$totalCount} connections")
                                ->{$successCount === $totalCount ? 'success' : 'warning'}()
                                ->send();
                        }),

                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalDescription('This will delete the selected Redis connections and remove them from all applications. This action cannot be undone.'),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
