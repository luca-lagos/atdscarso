<?php

namespace App\Filament\Docentes\Widgets;

use Filament\Widgets\Widget;
use App\Models\Turnos_sala;
use App\Models\Turnos_tv;
use App\Models\PrestamoBiblioteca;
use Illuminate\Support\Facades\Auth;

class DocenteDashboardWidget extends Widget
{
    protected string $view = 'filament.docentes.widgets.docente-dashboard-widget';

    protected int|string|array $columnSpan = 'full';

    public function getData(): array
    {
        $user = Auth::user();

        return [
            'turnosSala' => Turnos_sala::where('user_id', $user->id)
                ->whereDate('fecha_turno', '>=', now()->toDateString())
                ->orderBy('fecha_turno', 'asc')
                ->limit(5)
                ->get(),

            'turnosTv' => Turnos_tv::where('user_id', $user->id)
                ->whereDate('fecha_turno', '>=', now()->toDateString())
                ->orderBy('fecha_turno', 'asc')
                ->limit(5)
                ->get(),

            'prestamosBiblioteca' => PrestamoBiblioteca::where('user_id', $user->id)
                ->latest()
                ->limit(5)
                ->get(),
        ];
    }
}
