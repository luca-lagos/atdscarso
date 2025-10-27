<?php

namespace App\Filament\Resources\TurnosTvs\Widgets;

use App\Filament\Resources\TurnosTvs\Pages\CreateTurnosTv;
use App\Filament\Resources\TurnosTvs\Pages\EditTurnosTv;
use App\Models\Turnos_tv;
use Carbon\Carbon;
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Support\Collection;

class TurnosTvCalendarWidget extends CalendarWidget
{
    public string|\Illuminate\Support\HtmlString|null|bool $heading = 'Turnos de TV';

    protected bool $eventClickEnabled = true;
    protected ?string $defaultEventClickAction = 'edit';
    protected bool $useFilamentTimezone = true;
    protected bool $selectable = true;
    protected bool $editable = true;

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
        $turnos = Turnos_tv::query()
            ->with(['user', 'inventario'])
            ->whereBetween('fecha_turno', [$info->start->toDateString(), $info->end->toDateString()])
            ->get();

        return $turnos->map(function (Turnos_tv $t) {
            $start = Carbon::parse($t->fecha_turno . ' ' . $t->hora_inicio);
            $end   = Carbon::parse($t->fecha_turno . ' ' . $t->hora_fin);

            $title = trim(($t->inventario->nombre_equipo ?? 'TV') . ' · ' . ($t->user->name ?? 'Profesor'));

            // Colores por estado
            [$bg, $text, $border] = match ($t->estado) {
                'activo'     => ['#7B1E2B', '#ffffff', '#6b1a26'],
                'confirmado' => ['#2E7D32', '#ffffff', '#276c2b'],
                'finalizado' => ['#475569', '#ffffff', '#334155'],
                'cancelado'  => ['#C62828', '#ffffff', '#b12525'],
                default      => ['#64748B', '#ffffff', '#475569'],
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
                    'estado'   => $t->estado,
                    'profesor' => $t->user->nombre_completo ?? null,
                    'tv'       => $t->inventario->nombre_equipo ?? null,
                ],
            ];
        })->all();
    }

    protected function onSelect(array $selectInfo): ?string
    {
        $start = Carbon::parse($selectInfo['start']);
        $end   = Carbon::parse($selectInfo['end']);

        return CreateTurnosTv::getUrl([
            'fecha_turno' => $start->toDateString(),
            'hora_inicio' => $start->format('H:i'),
            'hora_fin'    => $end->format('H:i'),
        ]);
    }

    protected function updateEventTiming(array $event): void
    {
        $id = data_get($event, 'extendedProps.recordId') ?? data_get($event, 'id');
        if (! $id) return;

        $model = Turnos_tv::find($id);
        if (! $model) return;

        $start = Carbon::parse($event['start']);
        $end   = isset($event['end']) ? Carbon::parse($event['end']) : null;

        $model->fecha_turno = $start->toDateString();
        $model->hora_inicio = $start->format('H:i:s');

        if ($end) {
            $model->hora_fin = $end->format('H:i:s');
        }

        $model->save();
        $this->dispatch('refreshCalendar');
    }
}
