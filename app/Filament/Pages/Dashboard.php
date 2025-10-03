<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\StatsOverviewWidget;
use App\Filament\Resources\TurnosTvs\Widgets\TurnosTvCalendarWidget;
use App\Filament\Resources\TurnosSalas\Widgets\TurnosSalaCalendarWidget;


class Dashboard extends Page
{
    //protected string $view = 'filament.pages.dashboard';
    protected static ?string $navigationLabel = 'Escritorio';
    //protected static ?string $navigationIcon = 'heroicon-o-home';

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            TurnosTvCalendarWidget::class,
            TurnosSalaCalendarWidget::class,
        ];
    }
}
