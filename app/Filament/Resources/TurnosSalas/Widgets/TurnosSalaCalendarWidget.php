<?php

namespace App\Filament\Resources\TurnosSalas\Widgets;

use App\Filament\Resources\TurnosSalas\Pages\CreateTurnosSala;
use App\Models\Turnos_sala;
use Carbon\Carbon;
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Support\Collection;

class TurnosSalaCalendarWidget extends CalendarWidget
{
    public string|\Illuminate\Support\HtmlString|null|bool $heading = 'Turnos de sala de informática';

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
        $turnos = Turnos_sala::query()
            ->with('user')
            ->whereBetween('fecha_turno', [$info->start->toDateString(), $info->end->toDateString()])
            ->get();

        return $turnos->map(function (Turnos_sala $t) {
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

            // ✅ Título mejorado: "3°5° - Prof. García"
            $curso = $t->curso ? "{$t->curso}°{$t->division}" : 'Sin curso';
            $profesor = $t->user->name ?? 'Profesor';
            $title = "{$curso} - {$profesor}";

            // ✅ Paleta de colores coherente con tema Amber
            [$bg, $text] = match ($t->tipo) {
                'permanente' => ['#D97706', '#ffffff'], // Amber 600
                'temporal'   => ['#059669', '#ffffff'], // Emerald 600
                default      => ['#64748B', '#ffffff'], // Slate 500
            };

            return CalendarEvent::make()
                ->title($title)
                ->start($start)
                ->end($end)
                ->allDay(false)
                ->backgroundColor($bg)
                ->textColor($text)
                ->extendedProps([
                    'model' => Turnos_sala::class,
                    'key' => $t->getKey(),
                    'tipo' => $t->tipo,
                    'profesor' => $profesor,
                    'curso' => $curso,
                ]);
        });
    }

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
