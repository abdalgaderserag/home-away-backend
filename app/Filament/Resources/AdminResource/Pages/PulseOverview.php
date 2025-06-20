<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Dotswan\FilamentLaravelPulse\Widgets\PulseCache;
use Dotswan\FilamentLaravelPulse\Widgets\PulseExceptions;
use Dotswan\FilamentLaravelPulse\Widgets\PulseQueues;
use Dotswan\FilamentLaravelPulse\Widgets\PulseServers;
use Dotswan\FilamentLaravelPulse\Widgets\PulseSlowOutGoingRequests;
use Dotswan\FilamentLaravelPulse\Widgets\PulseSlowQueries;
use Dotswan\FilamentLaravelPulse\Widgets\PulseSlowRequests;
use Dotswan\FilamentLaravelPulse\Widgets\PulseUsage;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Enums\ActionSize;
use Filament\Widgets\Widget;

class PulseOverview extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar'; // Choose your icon
    protected static ?string $navigationLabel = 'Monitor Dashboard'; // How it appears in navigation
    protected static ?string $title = 'Application Health'; // Page title
    protected static ?string $slug = 'monitor'; // URL segment

    // If you want this to be the default dashboard, set it here
    // protected static bool $shouldRegisterNavigation = false; // Hide from navigation if it's your dashboard
    // protected static string $routePath = '/'; // For default dashboard

    protected static string $view = 'filament.pages.overview'; // Your Blade view

    public function getHeaderWidgets(): array
    {
        return [
            PulseServers::class,
            PulseUsage::class,
            PulseExceptions::class,
            PulseSlowRequests::class,
            PulseSlowQueries::class,
            PulseSlowOutGoingRequests::class,
            PulseQueues::class,
            PulseCache::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        // This is an example, you'd need to adapt it to actually apply the filters
        // to the Pulse widgets, which might require passing state down.
        return [
            ActionGroup::make([
                Action::make('1h')
                    ->action(fn () => $this->redirect(route('filament.admin.pages.overview', ['period' => '1_hour']))),
                Action::make('24h')
                    ->action(fn () => $this->redirect(route('filament.admin.pages.overview', ['period' => '24_hours']))),
                Action::make('7d')
                    ->action(fn () => $this->redirect(route('filament.admin.pages.overview', ['period' => '7_days']))),
            ])
            ->label(__('Filter'))
            ->icon('heroicon-m-funnel')
            ->size(ActionSize::Small)
            ->color('gray')
            ->button()
        ];
    }
}