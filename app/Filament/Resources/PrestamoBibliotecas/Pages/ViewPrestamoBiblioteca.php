<?php

namespace App\Filament\Resources\PrestamoBibliotecas\Pages;

use App\Filament\Resources\PrestamoBibliotecas\PrestamoBibliotecaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPrestamoBiblioteca extends ViewRecord
{
    protected static string $resource = PrestamoBibliotecaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
