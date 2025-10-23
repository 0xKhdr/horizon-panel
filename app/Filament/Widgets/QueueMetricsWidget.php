<?php

namespace App\Filament\Widgets;

use App\Models\QueueConfiguration;
use Filament\Widgets\Widget;

class QueueMetricsWidget extends Widget
{
    protected string $view = 'filament.widgets.queue-metrics-widget';

    protected static ?int $sort = 2;

    public function getViewData(): array
    {
        $totalConfigurations = QueueConfiguration::count();
        $activeConfigurations = QueueConfiguration::where('is_active', true)->count();
        $totalProcesses = QueueConfiguration::where('is_active', true)->sum('max_processes');
        $applicationsWithQueues = QueueConfiguration::distinct('application_id')->count();

        return [
            'totalConfigurations' => $totalConfigurations,
            'activeConfigurations' => $activeConfigurations,
            'totalProcesses' => $totalProcesses,
            'applicationsWithQueues' => $applicationsWithQueues,
        ];
    }
}
