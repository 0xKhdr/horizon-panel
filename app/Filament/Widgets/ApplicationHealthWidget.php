<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use Filament\Widgets\Widget;

class ApplicationHealthWidget extends Widget
{
    protected string $view = 'filament.widgets.application-health-widget';

    protected static ?int $sort = 3;

    public function getViewData(): array
    {
        $totalApplications = Application::count();
        $activeApplications = Application::where('is_active', true)->count();
        $applicationsWithConnections = Application::whereHas('redisConnections')->count();
        $applicationsWithoutConnections = Application::whereDoesntHave('redisConnections')->count();

        return [
            'totalApplications' => $totalApplications,
            'activeApplications' => $activeApplications,
            'applicationsWithConnections' => $applicationsWithConnections,
            'applicationsWithoutConnections' => $applicationsWithoutConnections,
        ];
    }
}
