<?php

namespace App\Filament\Resources\InventarioBibliotecas\Pages;

use App\Filament\Resources\InventarioBibliotecas\InventarioBibliotecaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInventarioBiblioteca extends ViewRecord
{
    protected static string $resource = InventarioBibliotecaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
