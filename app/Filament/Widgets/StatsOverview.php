<?php

namespace App\Filament\Widgets;

use App\Models\Inventario;
use App\Models\Prestamo;
use App\Models\Turnos_sala;
use App\Models\Turnos_tv;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Equipos disponibles', Inventario::where('estado', 'disponible')->count()),
            Stat::make('Préstamos activos', Prestamo::where('estado', 'activo')->count()),
            Stat::make('Turnos Sala (semana)', Turnos_sala::whereBetween('fecha_turno', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count()),
            Stat::make('Turnos TV (hoy)', Turnos_tv::whereDate('fecha_turno', today())->count()),
        ];
    }
}
