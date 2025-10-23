<?php

namespace App\Filament\Resources\RedisConnections\Pages;

use App\Filament\Resources\RedisConnections\RedisConnectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRedisConnections extends ListRecords
{
    protected static string $resource = RedisConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
