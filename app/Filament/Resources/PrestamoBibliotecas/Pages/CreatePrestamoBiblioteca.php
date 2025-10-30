<?php

namespace App\Filament\Resources\PrestamoBibliotecas\Pages;

use App\Filament\Resources\PrestamoBibliotecas\PrestamoBibliotecaResource;
use App\Models\PrestamoBiblioteca;
use Filament\Resources\Pages\CreateRecord;

class CreatePrestamoBiblioteca extends CreateRecord
{
    protected static string $resource = PrestamoBibliotecaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Si querés autocompletar fecha_prestamo si no viene
        $data['fecha_prestamo'] ??= now()->toDateString();

        return $data;
    }

    protected function beforeCreate(): void
    {
        $libroId = $this->form->getState()['inventario_biblioteca_id'] ?? null;

        if ($libroId) {
            $ocupado = PrestamoBiblioteca::query()
                ->where('inventario_biblioteca_id', $libroId)
                ->whereIn('estado', ['activo', 'vencido'])
                ->whereNull('fecha_devolucion')
                ->exists();

            if ($ocupado) {
                throw ValidationException::withMessages([
                    'inventario_biblioteca_id' => 'Este libro ya tiene un préstamo activo o vencido sin devolución.',
                ]);
            }
        }
    }
}
