<?php

namespace App\Filament\Resources\QueueConfigurations;

use App\Filament\Resources\QueueConfigurations\Pages\CreateQueueConfiguration;
use App\Filament\Resources\QueueConfigurations\Pages\EditQueueConfiguration;
use App\Filament\Resources\QueueConfigurations\Pages\ListQueueConfigurations;
use App\Filament\Resources\QueueConfigurations\Schemas\QueueConfigurationForm;
use App\Filament\Resources\QueueConfigurations\Tables\QueueConfigurationsTable;
use App\Models\QueueConfiguration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QueueConfigurationResource extends Resource
{
    protected static ?string $model = QueueConfiguration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static string|null|\UnitEnum $navigationGroup = 'Management';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return QueueConfigurationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QueueConfigurationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQueueConfigurations::route('/'),
            'create' => CreateQueueConfiguration::route('/create'),
            'edit' => EditQueueConfiguration::route('/{record}/edit'),
        ];
    }
}
