<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-home';

    protected string $view = 'filament-panels::pages.dashboard';

    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';
}
