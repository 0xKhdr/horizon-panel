<?php

namespace App\Filament\Resources\QueueConfigurations\Schemas;

use App\Models\Application;
use App\Models\RedisConnection;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QueueConfigurationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Application & Connection')
                    ->schema([
                        Select::make('application_id')
                            ->label('Application')
                            ->options(Application::where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    // Reset Redis connection when application changes
                                    $set('redis_connection_id', null);
                                }
                            }),

                        Select::make('redis_connection_id')
                            ->label('Redis Connection')
                            ->options(function (callable $get) {
                                $applicationId = $get('application_id');
                                if (! $applicationId) {
                                    return [];
                                }

                                return RedisConnection::where('is_active', true)
                                    ->whereHas('applications', function ($query) use ($applicationId) {
                                        $query->where('application_id', $applicationId);
                                    })
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->helperText('Only active Redis connections associated with the selected application are available.'),
                    ])->columns(2),

                Section::make('Queue Configuration')
                    ->schema([
                        KeyValue::make('queue_names')
                            ->label('Queue Names')
                            ->keyLabel('Queue')
                            ->valueLabel('Priority')
                            ->default(['default' => '1'])
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Define the queues to process and their priorities (higher numbers = higher priority).'),

                        Select::make('balance_strategy')
                            ->label('Balance Strategy')
                            ->options([
                                'auto' => 'Auto (recommended)',
                                'simple' => 'Simple',
                                'round-robin' => 'Round Robin',
                            ])
                            ->default('auto')
                            ->required(),
                    ]),

                Section::make('Process Management')
                    ->schema([
                        TextInput::make('min_processes')
                            ->label('Minimum Processes')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->default(1)
                            ->required()
                            ->helperText('Minimum number of worker processes to maintain.'),

                        TextInput::make('max_processes')
                            ->label('Maximum Processes')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(1000)
                            ->default(10)
                            ->required()
                            ->helperText('Maximum number of worker processes to spawn.'),

                        TextInput::make('tries')
                            ->label('Max Tries')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10)
                            ->default(3)
                            ->required()
                            ->helperText('Maximum number of times to attempt a failed job.'),

                        TextInput::make('timeout')
                            ->label('Timeout (seconds)')
                            ->numeric()
                            ->minValue(30)
                            ->maxValue(3600)
                            ->default(60)
                            ->required()
                            ->helperText('Maximum time in seconds for a job to complete.'),

                        TextInput::make('memory')
                            ->label('Memory Limit (MB)')
                            ->numeric()
                            ->minValue(64)
                            ->maxValue(4096)
                            ->default(128)
                            ->required()
                            ->helperText('Memory limit per worker process in megabytes.'),
                    ])->columns(2),

                Section::make('Settings')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive configurations will not be used by Horizon.'),
                    ]),
            ]);
    }
}
