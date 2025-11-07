<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Prestamo extends Model
{
    protected $table = 'prestamos';

    protected $fillable = [
        'inventario_id',
        'user_id',
        'fecha_prestamo',
        'fecha_devolucion',
        'estado',
        'renovaciones',
        'observaciones',
        'pdf_path',
    ];

    protected $casts = [
        'fecha_prestamo' => 'date',
        'fecha_devolucion' => 'date',
    ];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isActive()
    {
        return $this->estado === 'activo';
    }

    public function isClosed()
    {
        return $this->estado === 'cerrado';
    }

    public function isOverdue()
    {
        return $this->estado === 'vencido';
    }

    public function markAsClosed()
    {
        $this->estado = 'cerrado';
        $this->fecha_devolucion = now();
        $this->save();
    }

    public function markAsOverdue()
    {
        $this->estado = 'vencido';
        $this->save();
    }

    public function scopeActive($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeOverdue($query)
    {
        return $query->where('estado', 'vencido');
    }

    public function scopeClosed($query)
    {
        return $query->where('estado', 'cerrado');
    }

    public function getPdfUrlAttribute()
    {
        return $this->pdf_path
            ? Storage::url($this->pdf_path)
            : null;
    }
}
