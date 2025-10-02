<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turnos_sala extends Model
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
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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
}
