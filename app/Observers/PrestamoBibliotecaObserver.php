<?php

namespace App\Observers;

use App\Models\InventarioBiblioteca;
use App\Models\PrestamoBiblioteca;

class PrestamoBibliotecaObserver
{
    /**
     * Handle the PrestamoBiblioteca "created" event.
     */
    public function created(PrestamoBiblioteca $prestamoBiblioteca): void
    {
        //
    }

    /**
     * Handle the PrestamoBiblioteca "updated" event.
     */
    public function updated(PrestamoBiblioteca $prestamo): void
    {
        // Si el préstamo se finalizó (tiene fecha de devolución)
        if ($prestamo->isDirty('fecha_devolucion') && $prestamo->fecha_devolucion) {
            $this->liberarLibro($prestamo);
        }

        // Si el estado cambió a "finalizado" o "cancelado"
        if ($prestamo->isDirty('estado') && in_array($prestamo->estado, ['finalizado', 'cancelado'])) {
            $this->liberarLibro($prestamo);
        }
    }

    /**
     * Handle the PrestamoBiblioteca "deleted" event.
     */
    public function deleted(PrestamoBiblioteca $prestamo): void
    {
        $this->liberarLibro($prestamo);
    }

    /**
     * Liberar el libro (volver a estado "disponible")
     */
    private function liberarLibro(PrestamoBiblioteca $prestamo): void
    {
        if ($prestamo->inventario_biblioteca_id) {
            InventarioBiblioteca::where('id', $prestamo->inventario_biblioteca_id)
                ->update(['estado' => 'disponible']);
        }
    }

    /**
     * Handle the PrestamoBiblioteca "restored" event.
     */
    public function restored(PrestamoBiblioteca $prestamoBiblioteca): void
    {
        //
    }

    /**
     * Handle the PrestamoBiblioteca "force deleted" event.
     */
    public function forceDeleted(PrestamoBiblioteca $prestamoBiblioteca): void
    {
        //
    }
}
