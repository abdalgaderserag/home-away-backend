<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class OnlineUsersChart extends ChartWidget
{
    protected static ?string $heading = 'Live Logged-in Users';
    protected static ?string $pollingInterval = '10s';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $dataCacheTime = 300; // 5 minutes

    private const MAX_DATA_POINTS = 15;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $onlineCount = User::where('last_seen_at', '>=', now()->subMinutes(5))->count();
        $currentTime = now()->format('H:i:s');

        // Get historical data from cache
        $historicalData = Cache::remember('online_users_chart_data', now()->addMinutes(15), function () {
            return [
                'data' => [],
                'labels' => []
            ];
        });

        // Add new data point
        $historicalData['data'][] = $onlineCount;
        $historicalData['labels'][] = $currentTime;

        // Keep only the last MAX_DATA_POINTS entries
        if (count($historicalData['data']) > self::MAX_DATA_POINTS) {
            array_shift($historicalData['data']);
            array_shift($historicalData['labels']);
        }

        // Update cache
        Cache::put('online_users_chart_data', $historicalData, now()->addMinutes(15));

        return [
            'datasets' => [
                [
                    'label' => 'Online Users',
                    'data' => $historicalData['data'],
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#3B82F6',
                ],
            ],
            'labels' => $historicalData['labels'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'min' => 0,
                    'max' => max(10, User::count() * 1.1),
                    'ticks' => [
                        'stepSize' => 1,
                        'precision' => 0
                    ]
                ]
            ],
            'animation' => [
                'duration' => 500,
            ],
        ];
    }
}