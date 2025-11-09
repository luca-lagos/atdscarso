<?php

namespace App\Filament\Resources\PrestamoBibliotecas\Pages;

use App\Filament\Resources\PrestamoBibliotecas\PrestamoBibliotecaResource;
use App\Models\InventarioBiblioteca;
use App\Models\PrestamoBiblioteca;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreatePrestamoBiblioteca extends CreateRecord
{
    protected static string $resource = PrestamoBibliotecaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['fecha_prestamo'] ??= now()->toDateString();

        // Estado según rol del usuario
        if (!empty($data['user_id'])) {
            $user = User::find($data['user_id']);

            if ($user?->hasRole('profesor')) {
                $data['estado'] = 'activo';
            } elseif ($user?->hasRole('alumno')) {
                $data['estado'] = 'pendiente';
            } else {
                // por defecto, si no se reconoce, va pendiente
                $data['estado'] = 'pendiente';
            }
        }

        return $data;
    }

    protected function beforeCreate(): void
    {
        $state = $this->form->getState();
        $libroId = $state['inventario_biblioteca_id'] ?? null;

        // Evitar sobreasignar más que la cantidad disponible
        if ($libroId) {
            $libro = InventarioBiblioteca::find($libroId);
            $activos = $libro?->prestamos()
                ->whereIn('estado', ['pendiente', 'activo', 'vencido'])
                ->whereNull('fecha_devolucion')
                ->count() ?? 0;

            if ($libro && $activos >= (int) $libro->cantidad) {
                throw ValidationException::withMessages([
                    'inventario_biblioteca_id' => 'No hay ejemplares disponibles para préstamo.',
                ]);
            }
        }
    }

    protected function afterCreate(): void
    {
        $prestamo = $this->record;

        // Generamos PDF con una vista Blade
        $pdf = Pdf::loadView('pdf.comodato-biblioteca', ['prestamo' => $prestamo]);

        $filePath = "comodatos/biblioteca/comodato_{$prestamo->id}.pdf";

        Storage::put($filePath, $pdf->output());

        // Guardar la ruta en el registro
        $prestamo->update([
            'pdf_path' => $filePath,
        ]);

        Notification::make()
            ->title('Préstamo registrado correctamente')
            ->success()
            ->send();
    }
}
