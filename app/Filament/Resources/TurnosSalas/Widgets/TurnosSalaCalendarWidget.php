<?php

namespace App\Filament\Resources\TurnosSalas\Widgets;

use App\Models\Turnos_sala;
use Filament\Widgets\Widget;
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TurnosSalaCalendarWidget extends Widget
{
    //protected string $view = 'filament.resources.turnos-salas.widgets.turnos-sala-calendar-widget';

    public string|\Illuminate\Support\HtmlString|null|bool $heading = 'Turnos de sala de informatica';

    protected bool $eventClickEnabled = true;   
    protected ?string $defaultEventClickAction  = 'edit';
    protected bool $useFilamentTimezone = true;

    protected function getEvents(FetchInfo $info): Collection|array|Builder
    {
        // Solo eventos dentro del rango visible del calendario
        return Turnos_sala::query()
            ->whereDate('fecha_turno', '>=', $info->start)
            ->whereDate('fecha_turno', '<=', $info->end)
            ->with('user');
    }
}
