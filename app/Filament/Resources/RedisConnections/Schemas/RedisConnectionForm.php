<?php

namespace App\Filament\Resources\RedisConnections\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RedisConnectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('host')
                    ->required(),
                TextInput::make('port')
                    ->required()
                    ->numeric()
                    ->default(6379),
                Textarea::make('password')
                    ->columnSpanFull(),
                TextInput::make('database')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
