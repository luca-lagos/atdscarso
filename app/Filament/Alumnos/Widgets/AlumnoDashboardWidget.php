<?php

namespace App\Filament\Alumnos\Widgets;

use App\Models\PrestamoBiblioteca;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Reactive;

class AlumnoDashboardWidget extends Widget
{
    protected string $view = 'filament.alumnos.widgets.alumno-dashboard-widget';

    protected int | string | array $columnSpan = 'full';

    #[Reactive] // Esto hace que la propiedad sea reactiva y se pueda pasar desde el padre
    public ?string $selectedDate = null; // Definir la propiedad

    protected function getViewData(): array
    {
        $user = auth()->user();
        $hoy = Carbon::today();
        $finMes = $hoy->copy()->endOfMonth();

        // Últimos préstamos (lista top 5)
        $prestamos = PrestamoBiblioteca::query()
            ->where('user_id', $user->id)
            ->orderBy('fecha_prestamo', 'desc')
            ->limit(5)
            ->get();

        // ===== Eventos para mini-calendario (mes actual) =====
        $eventosPrestamosAlumno = PrestamoBiblioteca::query()
            ->where('user_id', $user->id)
            ->whereBetween('fecha_prestamo', [$hoy->copy()->startOfMonth(), $finMes])
            ->select(DB::raw('DATE(fecha_prestamo) as d'), DB::raw('count(*) as c'))
            ->groupBy('d')
            ->pluck('c', 'd')
            ->mapWithKeys(fn($count, $date) => [
                Carbon::parse($date)->format('Y-m-d') => $count
            ])
            ->toArray();

        return [
            'prestamos' => $prestamos,
            'eventosPrestamosAlumno' => $eventosPrestamosAlumno,
        ];
    }
}
