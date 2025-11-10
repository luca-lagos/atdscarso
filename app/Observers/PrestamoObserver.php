<?php

namespace App\Observers;

use App\Models\Inventario;
use App\Models\Prestamo;

class PrestamoObserver
{
    /**
     * Handle the Prestamo "created" event.
     */
    public function created(Prestamo $prestamo): void
    {
        //
    }

    /**
     * Handle the Prestamo "updated" event.
     */
    public function updated(Prestamo $prestamo): void
    {
        if ($prestamo->isDirty('fecha_devolucion') && $prestamo->fecha_devolucion) {
            $this->liberarEquipo($prestamo);
        }

        if ($prestamo->isDirty('estado') && $prestamo->estado === 'devuelto') {
            $this->liberarEquipo($prestamo);
        }
    }

    /**
     * Handle the Prestamo "deleted" event.
     */
    public function deleted(Prestamo $prestamo): void
    {
        $this->liberarEquipo($prestamo);
    }

    /**
     * Handle the Prestamo "restored" event.
     */
    public function restored(Prestamo $prestamo): void
    {
        //
    }

    /**
     * Handle the Prestamo "force deleted" event.
     */
    public function forceDeleted(Prestamo $prestamo): void
    {
        //
    }

    /**
     * Liberar el equipo (volver a estado "disponible")
     */
    private function liberarEquipo(Prestamo $prestamo): void
    {
        if ($prestamo->inventario_id) {
            Inventario::where('id', $prestamo->inventario_id)
                ->update(['estado' => 'disponible']);
        }
    }
}
