<?php

namespace App\Filament\Resources\RedisConnections;

use App\Filament\Resources\RedisConnections\Pages\CreateRedisConnection;
use App\Filament\Resources\RedisConnections\Pages\EditRedisConnection;
use App\Filament\Resources\RedisConnections\Pages\ListRedisConnections;
use App\Filament\Resources\RedisConnections\Schemas\RedisConnectionForm;
use App\Filament\Resources\RedisConnections\Tables\RedisConnectionsTable;
use App\Models\RedisConnection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RedisConnectionResource extends Resource
{
    protected static ?string $model = RedisConnection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'RedisConnection';

    public static function form(Schema $schema): Schema
    {
        return RedisConnectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RedisConnectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRedisConnections::route('/'),
            'create' => CreateRedisConnection::route('/create'),
            'edit' => EditRedisConnection::route('/{record}/edit'),
        ];
    }
}
