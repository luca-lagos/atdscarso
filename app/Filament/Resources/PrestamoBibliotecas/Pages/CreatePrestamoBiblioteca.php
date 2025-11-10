<?php

namespace App\Filament\Resources\PrestamoBibliotecas\Pages;

use App\Filament\Resources\PrestamoBibliotecas\PrestamoBibliotecaResource;
use App\Models\InventarioBiblioteca;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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
                $data['estado'] = 'pendiente';
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $prestamo = $this->record;

        info('Préstamo de biblioteca creado con ID: ' . $prestamo->inventarioBiblioteca);



        // Cambiar estado del libro a "prestado"
        if ($prestamo->inventario_biblioteca_id) {
            InventarioBiblioteca::where('id', $prestamo->inventario_biblioteca_id)
                ->update(['estado' => 'prestado']);
        }

        // Generar PDF
        $pdf = Pdf::loadView('pdf.comodato-biblioteca', ['prestamo' => $prestamo]);

        $filePath = "comodatos/biblioteca/comodato_{$prestamo->id}.pdf";

        Storage::disk('public')->put($filePath, $pdf->output());

        $prestamo->update([
            'pdf_path' => $filePath,
        ]);

        Notification::make()
            ->title('Préstamo registrado correctamente')
            ->success()
            ->send();
    }
}
