<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Guava\Calendar\Contracts\Eventable;
use Guava\Calendar\ValueObjects\CalendarEvent;

class Turnos_sala extends Model implements Eventable
{
    protected $table = 'turnos_sala';

    protected $fillable = [
        'user_id',
        'curso',
        'division',
        'fecha_turno',
        'hora_inicio',
        'hora_fin',
        'tipo',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'fecha_turno' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toCalendarEvent(): CalendarEvent
    {
        return CalendarEvent::make($this)
            ->title(($this->curso . ' ' . $this->division) . ' - ' . ($this->user?->nombre_completo ?? ''))
            ->start($this->fecha_turno->toDateString() . ' ' . $this->hora_inicio->format('H:i'))
            ->end($this->fecha_turno->toDateString() . ' ' . $this->hora_fin->format('H:i'));
    }

    public function isPermanente()
    {
        return $this->tipo === 'permanente';
    }

    public function isTemporal()
    {
        return $this->tipo === 'temporal';
    }

    public function scopePermanente($query)
    {
        return $query->where('tipo', 'permanente');
    }

    public function scopeTemporal($query)
    {
        return $query->where('tipo', 'temporal');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('fecha_turno', '>=', now()->toDateString());
    }

    public function scopePast($query)
    {
        return $query->where('fecha_turno', '<', now()->toDateString());
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function isFinalizado()
    {
        return $this->estado === 'finalizado';
    }

    public function scopeFinalizado($query)
    {
        return $query->where('estado', 'finalizado');
    }

    public function scopeVencido($query)
    {
        // Turnos cuya fecha + hora_fin ya pasó
        return $query->whereRaw("CONCAT(fecha_turno::text, ' ', hora_fin::text)::timestamp < NOW()");
    }
}
