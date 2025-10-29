<?php

namespace App\Filament\Resources\InventarioBibliotecas\Pages;

use App\Filament\Resources\InventarioBibliotecas\InventarioBibliotecaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditInventarioBiblioteca extends EditRecord
{
    protected static string $resource = InventarioBibliotecaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
