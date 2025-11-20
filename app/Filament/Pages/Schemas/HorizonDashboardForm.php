<?php

namespace App\Filament\Pages\Schemas;

use App\Models\Application;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class HorizonDashboardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('application_id')
                ->label('Application')
                ->options(Application::pluck('name', 'id'))
                ->reactive()
                ->afterStateUpdated(fn ($set) => $set('redis_connection_id', null))
                ->required(),

            Select::make('redis_connection_id')
                ->label('Redis Connection')
                ->options(function (callable $get) {
                    $applicationId = $get('application_id');
                    if (! $applicationId) {
                        return [];
                    }

                    return Application::find($applicationId)
                        ?->redisConnections()
                        ->pluck('name', 'redis_connections.id') ?? [];
                })
                ->required()
                ->reactive(),
        ]);
    }
}
