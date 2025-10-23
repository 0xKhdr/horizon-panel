<?php

namespace App\Filament\Resources\RedisConnections\Pages;

use App\Filament\Resources\RedisConnections\RedisConnectionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRedisConnection extends CreateRecord
{
    protected static string $resource = RedisConnectionResource::class;
}
