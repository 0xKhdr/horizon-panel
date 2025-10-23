<?php

namespace App\Filament\Resources\QueueConfigurations\Pages;

use App\Filament\Resources\QueueConfigurations\QueueConfigurationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQueueConfiguration extends CreateRecord
{
    protected static string $resource = QueueConfigurationResource::class;
}
