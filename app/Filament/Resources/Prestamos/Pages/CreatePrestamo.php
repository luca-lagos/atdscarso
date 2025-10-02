<?php

namespace App\Filament\Resources\Prestamos\Pages;

use App\Filament\Resources\Prestamos\PrestamoResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreatePrestamo extends CreateRecord
{
    protected static string $resource = PrestamoResource::class;

    protected function afterCreate(): void
    {
      $prestamo = $this->record;

        // Generamos PDF con una vista Blade
        $pdf = Pdf::loadView('pdf.comodato', ['prestamo' => $prestamo]);
        
        $filePath = "comodatos/comodato_{$prestamo->id}.pdf";

        Storage::put($filePath, $pdf->output());

        // Guardar la ruta en el registro
        $prestamo->update([
            'pdf_path' => $filePath,
        ]);  
    }
}
