<?php

namespace App\Filament\Resources\TurnosTvs\Widgets;

use App\Filament\Resources\TurnosTvs\Pages\CreateTurnosTv;
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
            'timeZone' => 'local',
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
            // Formatear fecha y horas
            $fechaStr = $t->fecha_turno instanceof Carbon
                ? $t->fecha_turno->format('Y-m-d')
                : $t->fecha_turno;

            $horaInicio = $t->hora_inicio instanceof Carbon
                ? $t->hora_inicio->format('H:i:s')
                : $t->hora_inicio;

            $horaFin = $t->hora_fin instanceof Carbon
                ? $t->hora_fin->format('H:i:s')
                : $t->hora_fin;

            $start = $fechaStr . 'T' . substr($horaInicio, 0, 5) . ':00';
            $end   = $fechaStr . 'T' . substr($horaFin, 0, 5) . ':00';

            // ✅ Título mejorado: "TV Samsung - Prof. Martínez"
            $tvName = $t->inventario->nombre_equipo ?? 'TV';
            $profesor = $t->user->name ?? 'Profesor';
            $title = "{$tvName} - {$profesor}";

            // ✅ Paleta de colores coherente con tema Amber
            [$bg, $text] = match ($t->estado) {
                'activo'     => ['#D97706', '#ffffff'], // Amber 600
                'confirmado' => ['#059669', '#ffffff'], // Emerald 600
                'finalizado' => ['#64748B', '#ffffff'], // Slate 500
                'cancelado'  => ['#DC2626', '#ffffff'], // Red 600
                default      => ['#94A3B8', '#ffffff'], // Slate 400
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
                    'profesor' => $profesor,
                    'tv'       => $tvName,
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
