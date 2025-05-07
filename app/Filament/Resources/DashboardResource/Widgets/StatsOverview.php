<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Models\Offer;
use App\Models\Project;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::count())            
                ->description('Total registered users'),
            // Stat::make('Clients', User::where('type', 'client')->count()),
            // Stat::make('Designers', User::where('type', 'designer')->count()),
            Stat::make('Projects', Project::count())
                ->description('Total projects'),
            Stat::make('Offer', Offer::count())
                ->description('Total offers'),
        ];
    }
}
