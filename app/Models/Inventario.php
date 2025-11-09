<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $table = 'inventario';

    protected $fillable = [
        'nombre_equipo',
        'categoria',
        'marca',
        'modelo',
        'nro_serie',
        'estado',
        'cantidad',
        'observaciones',
    ];

    public function prestamos()
    {
        return $this->hasMany(Prestamo::class);
    }

    public function turnosTv()
    {
        return $this->hasMany(Turnos_tv::class, 'inventario_id');
    }

    public function isAvailable()
    {
        return $this->estado === 'disponible';
    }

    public function markAsPrestado()
    {
        $this->estado = 'prestado';
        $this->save();
    }

    public function markAsDisponible()
    {
        $this->estado = 'disponible';
        $this->save();
    }

    public function markAsEnReparacion()
    {
        $this->estado = 'en_reparacion';
        $this->save();
    }

    public function markAsBaja()
    {
        $this->estado = 'baja';
        $this->save();
    }

    public function scopeAvailable($query)
    {
        return $query->where('estado', 'disponible');
    }

    public function scopePrestado($query)
    {
        return $query->where('estado', 'prestado');
    }

    public function scopeEnReparacion($query)
    {
        return $query->where('estado', 'en_reparacion');
    }

    public function scopeBaja($query)
    {
        return $query->where('estado', 'baja');
    }
}
