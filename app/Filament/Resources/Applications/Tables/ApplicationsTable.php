<?php

namespace App\Filament\Resources\Applications\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                IconColumn::make('icon')
                    ->icon(fn (string $state): string => $state ?: 'heroicon-o-cube'),

                ColorColumn::make('color'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('redis_connections_count')
                    ->label('Redis Connections')
                    ->counts('redisConnections')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
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

                SelectFilter::make('redis_connections_count')
                    ->label('Connections Count')
                    ->options([
                        '0' => 'No connections',
                        '1-5' => '1-5 connections',
                        '6-10' => '6-10 connections',
                        '10+' => '10+ connections',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === '0',
                            fn (Builder $query): Builder => $query->doesntHave('redisConnections'),
                        )
                        ->when(
                            $data['value'] === '1-5',
                            fn (Builder $query): Builder => $query->withCount('redisConnections')->having('redis_connections_count', '>=', 1)->having('redis_connections_count', '<=', 5),
                        )
                        ->when(
                            $data['value'] === '6-10',
                            fn (Builder $query): Builder => $query->withCount('redisConnections')->having('redis_connections_count', '>=', 6)->having('redis_connections_count', '<=', 10),
                        )
                        ->when(
                            $data['value'] === '10+',
                            fn (Builder $query): Builder => $query->withCount('redisConnections')->having('redis_connections_count', '>=', 11),
                        );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalDescription('This will permanently delete the selected applications and all associated Redis connections and queue configurations. This action cannot be undone.'),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
