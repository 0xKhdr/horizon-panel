<?php

namespace App\Filament\Resources\RedisConnections\Pages;

use App\Filament\Resources\RedisConnections\RedisConnectionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditRedisConnection extends EditRecord
{
    protected static string $resource = RedisConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
