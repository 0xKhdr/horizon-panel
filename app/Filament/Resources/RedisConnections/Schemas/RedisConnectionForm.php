<?php

namespace App\Filament\Resources\RedisConnections\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RedisConnectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Connection Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->live(onBlur: true),

                        TextInput::make('host')
                            ->required()
                            ->maxLength(255)
                            ->default('localhost')
                            ->live(onBlur: true),

                        TextInput::make('port')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(65535)
                            ->default(6379)
                            ->live(onBlur: true),

                        TextInput::make('password')
                            ->label('Password')
                            ->revealable()
                            ->password()
                            ->maxLength(255)
                            ->helperText('Leave empty if no password is required'),

                        TextInput::make('database')
                            ->label('Database')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(15)
                            ->default(0)
                            ->live(onBlur: true),
                    ])->columns(2),

                Section::make('Advanced Options')
                    ->schema([
                        KeyValue::make('options')
                            ->label('Redis Options')
                            ->keyLabel('Option')
                            ->valueLabel('Value')
                            ->default([])
                            ->columnSpanFull()
                            ->helperText('Advanced Redis client options (e.g., timeout, retry, etc.)'),

                        TextInput::make('environment')
                            ->maxLength(50)
                            ->default('production')
                            ->placeholder('production'),

                        TextInput::make('region')
                            ->maxLength(50)
                            ->placeholder('us-east-1'),

                        TextInput::make('provider')
                            ->maxLength(50)
                            ->placeholder('AWS ElastiCache'),

                        Textarea::make('notes')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),

                Section::make('Status')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive connections will not be available for applications.'),
                    ]),
            ]);
    }
}
