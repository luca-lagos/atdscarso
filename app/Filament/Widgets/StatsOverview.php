<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Inventarios\Pages\ListInventarios;
use App\Filament\Resources\Prestamos\Pages\ListPrestamos;
use App\Filament\Resources\TurnosSalas\Pages\ListTurnosSalas;
use App\Filament\Resources\TurnosTvs\Pages\ListTurnosTvs;
use App\Models\Inventario;
use App\Models\Prestamo;
use App\Models\Turnos_sala;
use App\Models\Turnos_tv;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class StatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Resumen';

    protected function getStats(): array
    {
        // Fechas de referencia
        $hoy = Carbon::today();
        $inicioSemana = Carbon::now()->startOfWeek();
        $finSemana = Carbon::now()->endOfWeek();

        // Métricas actuales
        $equiposDisponibles = Inventario::where('estado', 'disponible')->count();
        $prestamosActivos   = Prestamo::where('estado', 'activo')->count();
        $turnosSalaSemana   = Turnos_sala::whereBetween('fecha_turno', [$inicioSemana, $finSemana])->count();
        $turnosTvHoy        = Turnos_tv::whereDate('fecha_turno', $hoy)->count();

        // Métricas de comparación (opcional, si querés mostrar tendencia)
        $equiposDisponiblesPrev = Inventario::where('estado', 'disponible')->count(); // mismo valor; para inventario podrías comparar con total si querés
        $prestamosAyer          = Prestamo::where('estado', 'activo')
            ->whereDate('created_at', $hoy->copy()->subDay())
            ->count();
        $turnosSalaSemanaPrev   = Turnos_sala::whereBetween('fecha_turno', [
            $inicioSemana->copy()->subWeek(),
            $finSemana->copy()->subWeek(),
        ])->count();
        $turnosTvAyer           = Turnos_tv::whereDate('fecha_turno', $hoy->copy()->subDay())->count();

        // Helpers
        $fmt = fn(int $n) => number_format($n, 0, ',', '.');
        $trend = function (int $current, int $previous): array {
            if ($previous === 0) {
                return [$current > 0 ? 'up' : 'neutral', $previous]; // evita división por 0
            }
            $delta = $current - $previous;
            return [$delta > 0 ? 'up' : ($delta < 0 ? 'down' : 'neutral'), $previous];
        };

        [$prestamosTrend, $prestamosPrev]     = $trend($prestamosActivos, $prestamosAyer);
        [$salaTrend, $salaPrev]               = $trend($turnosSalaSemana, $turnosSalaSemanaPrev);
        [$tvTrend, $tvPrev]                   = $trend($turnosTvHoy, $turnosTvAyer);

        return [
            Stat::make('Equipos disponibles', $fmt($equiposDisponibles))
                ->description('En stock para préstamo')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'onclick' => "window.location.href='" . e(ListInventarios::getUrl()) . "'",
                    'title' => 'Ver inventario',
                ]),

            Stat::make('Préstamos activos', $fmt($prestamosActivos))
                ->description(match ($prestamosTrend) {
                    'up'     => '↑ vs. ayer: ' . $fmt($prestamosPrev),
                    'down'   => '↓ vs. ayer: ' . $fmt($prestamosPrev),
                    default  => '= vs. ayer: ' . $fmt($prestamosPrev),
                })
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'onclick' => "window.location.href='" . e(ListPrestamos::getUrl()) . "'",
                    'title' => 'Ver préstamos',
                ]),

            Stat::make('Turnos Sala (semana)', $fmt($turnosSalaSemana))
                ->description(match ($salaTrend) {
                    'up'     => '↑ vs. semana pasada: ' . $fmt($salaPrev),
                    'down'   => '↓ vs. semana pasada: ' . $fmt($salaPrev),
                    default  => '= vs. semana pasada: ' . $fmt($salaPrev),
                })
                ->descriptionIcon('heroicon-m-computer-desktop')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'onclick' => "window.location.href='" . e(ListTurnosSalas::getUrl()) . "'",
                    'title' => 'Ver turnos de sala',
                ]),

            Stat::make('Turnos TV (hoy)', $fmt($turnosTvHoy))
                ->description(match ($tvTrend) {
                    'up'     => '↑ vs. ayer: ' . $fmt($tvPrev),
                    'down'   => '↓ vs. ayer: ' . $fmt($tvPrev),
                    default  => '= vs. ayer: ' . $fmt($tvPrev),
                })
                ->descriptionIcon('heroicon-m-tv')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'onclick' => "window.location.href='" . e(ListTurnosTvs::getUrl()) . "'",
                    'title' => 'Ver turnos de TV',
                ]),
        ];
    }
}
