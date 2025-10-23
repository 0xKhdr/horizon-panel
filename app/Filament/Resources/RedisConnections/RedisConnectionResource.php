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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RedisConnectionResource extends Resource
{
    protected static ?string $model = RedisConnection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedServerStack;

    protected static string|null|\UnitEnum $navigationGroup = 'Management';

    protected static ?int $navigationSort = 2;

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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
