<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioBiblioteca extends Model
{
    use HasFactory;

    protected $table = 'inventario_biblioteca';

    protected $fillable = [
        'titulo',
        'isbn',
        'autor',
        'editorial',
        'coleccion',
        'numero_edicion',
        'categoria',
        'idioma',
        'cantidad',
        'fecha_edicion',
        'fecha_entrada',
        'procedencia',
        'descripcion',
        'portada_path',
    ];
    public function prestamos()
    {
        return $this->hasMany(PrestamoBiblioteca::class, 'inventario_biblioteca_id');
    }

    public function scopeDisponibles($query)
    {
        // Disponible si no tiene préstamo activo/vencido sin devolución
        return $query->whereDoesntHave('prestamos', function ($q) {
            $q->whereIn('estado', ['activo', 'vencido'])->whereNull('fecha_devolucion');
        });
    }

    public function getPrestamosActivosCountAttribute(): int
    {
        return $this->prestamos()
            ->whereIn('estado', ['pendiente', 'activo', 'vencido'])
            ->whereNull('fecha_devolucion')
            ->count();
    }

    public function getDisponiblesCountAttribute(): int
    {
        return max(0, (int) ($this->cantidad ?? 0) - $this->prestamos_activos_count);
    }

    public function getDisponibleAttribute(): bool
    {
        return $this->disponibles_count > 0;
    }
}
