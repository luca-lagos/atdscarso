<?php

namespace App\Filament\Resources\TurnosSalas\Widgets;

use App\Filament\Resources\TurnosSalas\Pages\CreateTurnosSala;
use App\Filament\Resources\TurnosSalas\Pages\EditTurnosSala;
use App\Models\Turnos_sala;
use Carbon\Carbon;
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Support\Collection;

class TurnosSalaCalendarWidget extends CalendarWidget
{
    public string|\Illuminate\Support\HtmlString|null|bool $heading = 'Turnos de sala de informática';

    // Habilitamos interacciones
    protected bool $eventClickEnabled = true;
    protected ?string $defaultEventClickAction = 'edit'; // manejamos manualmente
    protected bool $useFilamentTimezone = true;
    protected bool $selectable = true;
    protected bool $editable = true; // drag & drop

    // Opciones visuales de FullCalendar a través del widget Guava
    protected function getCalendarOptions(): array
    {
        return [
            'locale' => 'es',
            'initialView' => 'dayGridMonth',
            'height' => 'auto',
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
            ],
            'buttonText' => [
                'today' => 'Hoy',
                'month' => 'Mes',
                'week'  => 'Semana',
                'day'   => 'Día',
                'list'  => 'Lista',
            ],
            'allDayText' => 'Todo el día',
            'weekText' => 'Sm',
            'moreLinkText' => 'más',
            'noEventsText' => 'No hay eventos para mostrar',
            'selectMirror' => true,
            'eventTimeFormat' => ['hour' => '2-digit', 'minute' => '2-digit', 'hour12' => false],
        ];
    }

    protected function getEvents(FetchInfo $info): Collection|array
    {
        $turnos = Turnos_sala::query()
            ->with('user')
            ->whereBetween('fecha_turno', [$info->start->toDateString(), $info->end->toDateString()])
            ->get();

        return $turnos->map(function (Turnos_sala $t) {
            $start = Carbon::parse($t->fecha_turno . ' ' . $t->hora_inicio);
            $end   = Carbon::parse($t->fecha_turno . ' ' . $t->hora_fin);

            $title = trim(($t->curso ? "{$t->curso} {$t->division}" : 'Sin curso') . ' · ' . ($t->user->name ?? 'Profesor'));

            // Colores por tipo
            [$bg, $text, $border] = match ($t->tipo) {
                'permanente' => ['#7B1E2B', '#ffffff', '#6b1a26'], // bordó
                'temporal'   => ['#2E7D32', '#ffffff', '#276c2b'], // verde
                default      => ['#475569', '#ffffff', '#334155'],
            };

            return [
                'id'    => (string) $t->getKey(),
                'title' => $title,
                'start' => $start->toIso8601String(),
                'end'   => $end->toIso8601String(),
                'allDay' => false,
                'backgroundColor' => $bg,
                'borderColor' => $border,
                'textColor' => $text,
                'extendedProps' => [
                    'recordId' => $t->getKey(),
                    'tipo' => $t->tipo,
                    'profesor' => $t->user->nombre_completo ?? null,
                ],
            ];
        })->all();
    }

    // Seleccionar rango vacío: ir a Create con datos prellenados
    protected function onSelect(array $selectInfo): ?string
    {
        $start = Carbon::parse($selectInfo['start']);
        $end   = Carbon::parse($selectInfo['end']);

        return CreateTurnosSala::getUrl([
            'fecha_turno'  => $start->toDateString(),
            'hora_inicio'  => $start->format('H:i'),
            'hora_fin'     => $end->format('H:i'),
        ]);
    }

    protected function updateEventTiming(array $event): void
    {
        $id = data_get($event, 'extendedProps.recordId') ?? data_get($event, 'id');
        if (! $id) return;

        $model = Turnos_sala::find($id);
        if (! $model) return;

        $start = Carbon::parse($event['start']);
        $end   = isset($event['end']) ? Carbon::parse($event['end']) : null;

        $model->fecha_turno = $start->toDateString();
        $model->hora_inicio = $start->format('H:i:s');

        if ($end) {
            $model->hora_fin = $end->format('H:i:s');
        }

        $model->save();
        $this->dispatch('refreshCalendar'); // Guava refresca
    }
}
