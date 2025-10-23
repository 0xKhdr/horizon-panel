<?php

namespace App\Filament\Resources\QueueConfigurations\Pages;

use App\Filament\Resources\QueueConfigurations\QueueConfigurationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQueueConfiguration extends EditRecord
{
    protected static string $resource = QueueConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
