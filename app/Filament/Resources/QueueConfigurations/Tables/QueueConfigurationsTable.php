<?php

namespace App\Filament\Resources\QueueConfigurations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QueueConfigurationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('application.name')
                    ->searchable(),
                TextColumn::make('redisConnection.name')
                    ->searchable(),
                TextColumn::make('balance_strategy')
                    ->searchable(),
                TextColumn::make('min_processes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_processes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tries')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('timeout')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('memory')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
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
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
