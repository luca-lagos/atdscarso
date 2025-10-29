<?php

namespace App\Filament\Resources\InventarioBibliotecas\Pages;

use App\Filament\Resources\InventarioBibliotecas\InventarioBibliotecaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInventarioBibliotecas extends ListRecords
{
    protected static string $resource = InventarioBibliotecaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
