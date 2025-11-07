<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PrestamoBiblioteca extends Model
{
    use HasFactory;

    protected $table = 'prestamos_biblioteca';

    protected $fillable = [
        'inventario_biblioteca_id',
        'user_id',
        'fecha_prestamo',
        'fecha_vencimiento',
        'fecha_devolucion',
        'estado',
        'renovaciones',
        'observaciones',
        'pdf_path',
    ];

    protected $casts = [
        'fecha_prestamo' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_devolucion' => 'date',
    ];

    public function libro()
    {
        return $this->belongsTo(InventarioBiblioteca::class, 'inventario_biblioteca_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeActivos($q)
    {
        return $q->where('estado', 'activo');
    }

    public function getPdfUrlAttribute()
    {
        return $this->pdf_path
            ? Storage::url($this->pdf_path)
            : null;
    }

    public function marcarDevuelto(?Carbon $fecha = null): void
    {
        $this->update([
            'fecha_devolucion' => $fecha ?? now()->toDateString(),
            'estado' => 'devuelto',
        ]);
    }

    public function renovar(int $dias = 7): void
    {
        // regla simple: no renovar si ya fue devuelto o perdido
        if (in_array($this->estado, ['devuelto', 'perdido'])) {
            return;
        }
        $this->update([
            'fecha_vencimiento' => ($this->fecha_vencimiento ?? now())->copy()->addDays($dias),
            'renovaciones' => $this->renovaciones + 1,
            'estado' => 'activo',
        ]);
    }
}
