<?php

namespace App\Filament\Resources\Prestamos\Pages;

use App\Filament\Resources\Prestamos\PrestamoResource;
use App\Models\Inventario;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreatePrestamo extends CreateRecord
{
    protected static string $resource = PrestamoResource::class;

    /*protected function mutateFormDataBeforeCreate(array $data): array
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
    }*/

    protected function beforeCreate(): void
    {
        $state = $this->form->getState();
        $equipoId = $state['inventario_id'] ?? null;

        // Evitar sobreasignar más que la cantidad disponible
        if ($equipoId) {
            $equipo = Inventario::find($equipoId);
            $activos = $equipo?->prestamos()
                ->whereIn('estado', ['activo', 'devuelto', 'vencido'])
                ->whereNull('fecha_devolucion')
                ->count() ?? 0;

            if ($equipo && $activos >= (int) $equipo->cantidad) {
                throw ValidationException::withMessages([
                    'inventario_id' => 'No hay ejemplares disponibles para préstamo.',
                ]);
            }
        }
    }

    protected function afterCreate(): void
    {
        $prestamo = $this->record;

        // Generamos PDF con una vista Blade
        $pdf = Pdf::loadView('pdf.comodato-informatica', ['prestamo' => $prestamo]);

        $filePath = "comodatos/informatica/comodato_{$prestamo->id}.pdf";

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
