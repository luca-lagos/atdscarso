<?php

namespace App\Filament\Docentes\Widgets;

use App\Models\PrestamoBiblioteca;
use App\Models\Turnos_sala;
use App\Models\Turnos_tv;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class DocenteDashboardWidget extends Widget
{
    protected string $view = 'filament.docentes.widgets.docente-dashboard-widget';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $user = auth()->user();
        $hoy = Carbon::today();
        $finMes = $hoy->copy()->endOfMonth();

        // Próximos turnos de sala (lista top 5)
        $turnosSala = Turnos_sala::query()
            ->where('user_id', $user->id)
            ->where('fecha_turno', '>=', $hoy)
            ->where('estado', 'activo')
            ->orderBy('fecha_turno')
            ->orderBy('hora_inicio')
            ->limit(5)
            ->get();

        // Próximos turnos de TV (lista top 5)
        $turnosTv = Turnos_tv::query()
            ->where('user_id', $user->id)
            ->where('fecha_turno', '>=', $hoy)
            ->where('estado', 'activo')
            ->orderBy('fecha_turno')
            ->orderBy('hora_inicio')
            ->limit(5)
            ->get();

        // Últimos préstamos de biblioteca (lista top 5)
        $prestamosBiblioteca = PrestamoBiblioteca::query()
            ->where('user_id', $user->id)
            ->where('estado', 'activo')
            ->orderBy('fecha_prestamo', 'desc')
            ->limit(5)
            ->get();

        // ===== Eventos para mini-calendarios (mes actual) =====

        // Turnos de Sala: agrupados por fecha
        $eventosSala = Turnos_sala::query()
            ->where('user_id', $user->id)
            ->where('estado', 'activo')
            ->whereBetween('fecha_turno', [$hoy->copy()->startOfMonth(), $finMes])
            ->select(DB::raw('DATE(fecha_turno) as d'), DB::raw('count(*) as c'))
            ->groupBy('d')
            ->pluck('c', 'd')
            ->mapWithKeys(fn($count, $date) => [
                Carbon::parse($date)->format('Y-m-d') => $count
            ])
            ->toArray();

        // Turnos de TV: agrupados por fecha
        $eventosTv = Turnos_tv::query()
            ->where('user_id', $user->id)
            ->where('estado', 'activo')
            ->whereBetween('fecha_turno', [$hoy->copy()
                ->startOfMonth(), $finMes])
            ->select(DB::raw('DATE(fecha_turno) as d'), DB::raw('count(*) as c'))
            ->groupBy('d')
            ->pluck('c', 'd')
            ->mapWithKeys(fn($count, $date) => [
                Carbon::parse($date)->format('Y-m-d') => $count
            ])
            ->toArray();

        // Préstamos de Biblioteca: agrupados por fecha de préstamo
        $eventosPrestamosDocente = PrestamoBiblioteca::query()
            ->where('user_id', $user->id)
            ->where('estado', 'activo')
            ->whereBetween('fecha_prestamo', [$hoy->copy()->startOfMonth(), $finMes])
            ->select(DB::raw('DATE(fecha_prestamo) as d'), DB::raw('count(*) as c'))
            ->groupBy('d')
            ->pluck('c', 'd')
            ->mapWithKeys(fn($count, $date) => [
                Carbon::parse($date)->format('Y-m-d') => $count
            ])
            ->toArray();

        return [
            'turnosSala' => $turnosSala,
            'turnosTv' => $turnosTv,
            'prestamosBiblioteca' => $prestamosBiblioteca,
            'eventosSala' => $eventosSala,
            'eventosTv' => $eventosTv,
            'eventosPrestamosDocente' => $eventosPrestamosDocente,
        ];
    }
}
