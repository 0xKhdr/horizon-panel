<?php

namespace App\Filament\Widgets;

use App\Models\RedisConnection;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\Widget;

class RedisHealthWidget extends Widget
{
    protected string $view = 'filament.widgets.redis-health-widget';

    protected static ?int $sort = 1;

    public function getViewData(): array
    {
        $totalConnections = RedisConnection::count();
        $activeConnections = RedisConnection::where('is_active', true)->count();
        $healthyConnections = RedisConnection::where('health_status', 'healthy')->count();
        $unhealthyConnections = RedisConnection::where('health_status', 'unhealthy')->count();

        return [
            'totalConnections' => $totalConnections,
            'activeConnections' => $activeConnections,
            'healthyConnections' => $healthyConnections,
            'unhealthyConnections' => $unhealthyConnections,
            'healthPercentage' => $totalConnections > 0 ? round(($healthyConnections / $totalConnections) * 100, 1) : 0,
        ];
    }
}
