<?php

namespace App\Filament\Resources\QueueConfigurations\Pages;

use App\Filament\Resources\QueueConfigurations\QueueConfigurationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQueueConfigurations extends ListRecords
{
    protected static string $resource = QueueConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
