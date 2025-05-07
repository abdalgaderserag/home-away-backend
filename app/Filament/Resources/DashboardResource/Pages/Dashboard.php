<?php

namespace App\Filament\Resources\DashboardResource\Pages;

use App\Filament\Resources\DashboardResource;
use App\Filament\Resources\DashboardResource\Widgets\OnlineUsersChart;
use App\Filament\Resources\DashboardResource\Widgets\StatsOverview;
use Filament\Resources\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            OnlineUsersChart::class,
        ];
    }
}
