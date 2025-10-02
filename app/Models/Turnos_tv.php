<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turnos_tv extends Model
{
    protected $table = 'turnos_tv';

    protected $fillable = [
        'user_id',
        'inventario_id',
        'fecha_turno',
        'hora_inicio',
        'hora_fin',
        'estado',
        'observaciones',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'inventario_id');
    }

    public function isActivo()
    {
        return $this->estado === 'activo';
    }

    public function isConfirmado()
    {
        return $this->estado === 'confirmado';
    }

    public function isCancelado()
    {
        return $this->estado === 'cancelado';
    }

    public function isFinalizado()
    {
        return $this->estado === 'finalizado';
    }

    public function scopeActivo($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeConfirmado($query)
    {
        return $query->where('estado', 'confirmado');
    }

    public function scopeCancelado($query)
    {
        return $query->where('estado', 'cancelado');
    }

    public function scopeFinalizado($query)
    {
        return $query->where('estado', 'finalizado');
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

    // App/Models/TurnosTv.php
public function overlaps($fecha, $horaInicio, $horaFin)
{
    return self::where('inventario_id', $this->inventario_id)
        ->where('fecha_turno', $fecha)
        ->where(function($q) use ($horaInicio, $horaFin) {
            $q->whereBetween('hora_inicio', [$horaInicio, $horaFin])
              ->orWhereBetween('hora_fin', [$horaInicio, $horaFin])
              ->orWhere(function($q2) use ($horaInicio, $horaFin) {
                  $q2->where('hora_inicio', '<=', $horaInicio)
                     ->where('hora_fin', '>=', $horaFin);
              });
        })
        ->exists();
}
}
