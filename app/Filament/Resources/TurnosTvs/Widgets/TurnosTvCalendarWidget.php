<?php

namespace App\Filament\Resources\TurnosTvs\Widgets;

use App\Filament\Resources\TurnosTvs\Pages\CreateTurnosTv;
use App\Filament\Resources\TurnosTvs\Pages\EditTurnosTv;
use App\Models\Turnos_tv;
use Carbon\Carbon;
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\CalendarEvent;
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
            'timeZone' => 'local',  // ← Agregar esta línea
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
            // Asegurar que fecha_turno sea string en formato Y-m-d
            $fechaStr = $t->fecha_turno instanceof Carbon
                ? $t->fecha_turno->format('Y-m-d')
                : $t->fecha_turno;

            // Asegurar que las horas sean strings en formato H:i:s
            $horaInicio = $t->hora_inicio instanceof Carbon
                ? $t->hora_inicio->format('H:i:s')
                : $t->hora_inicio;

            $horaFin = $t->hora_fin instanceof Carbon
                ? $t->hora_fin->format('H:i:s')
                : $t->hora_fin;

            // Crear las fechas completas en formato ISO
            $start = $fechaStr . 'T' . substr($horaInicio, 0, 5) . ':00';
            $end   = $fechaStr . 'T' . substr($horaFin, 0, 5) . ':00';

            $title = trim(($t->inventario->nombre_equipo ?? 'TV') . ' · ' . ($t->user->name ?? 'Profesor'));

            [$bg, $text, $border] = match ($t->estado) {
                'activo'     => ['#7B1E2B', '#ffffff', '#6b1a26'],
                'confirmado' => ['#2E7D32', '#ffffff', '#276c2b'],
                'finalizado' => ['#475569', '#ffffff', '#334155'],
                'cancelado'  => ['#C62828', '#ffffff', '#b12525'],
                default      => ['#64748B', '#ffffff', '#475569'],
            };

            return CalendarEvent::make()
                ->title($title)
                ->start($start)
                ->end($end)
                ->allDay(false)
                ->backgroundColor($bg)
                ->textColor($text)
                ->extendedProps([
                    'model' => Turnos_tv::class,
                    'key' => $t->getKey(),
                    'estado'   => $t->estado,
                    'profesor' => $t->user->name ?? null,
                    'tv'       => $t->inventario->nombre_equipo ?? null,
                ]);
        });
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
        // Guava Calendar ya resuelve el modelo automáticamente
        // si usás 'model' y 'key' en extendedProps
        $modelClass = data_get($event, 'extendedProps.model');
        $key = data_get($event, 'extendedProps.key') ?? data_get($event, 'id');

        if (!$modelClass || !$key) {
            return;
        }

        $model = $modelClass::find($key);
        if (!$model) {
            return;
        }

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
