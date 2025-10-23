<?php

namespace App\Filament\Resources\Applications\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Application Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            ),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->rules(['alpha_dash']),

                        Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),

                        TextInput::make('icon')
                            ->maxLength(50)
                            ->placeholder('heroicon-o-cube'),

                        ColorPicker::make('color')
                            ->default('#3B82F6')
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive applications will not be available for new Redis connections.'),
                    ])->columns(2),

                Section::make('Metadata')
                    ->schema([
                        KeyValue::make('metadata')
                            ->label('Custom Metadata')
                            ->keyLabel('Key')
                            ->valueLabel('Value')
                            ->default([])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
