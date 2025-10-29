<?php

namespace App\Filament\Resources\PrestamoBibliotecas\Pages;

use App\Filament\Resources\PrestamoBibliotecas\PrestamoBibliotecaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPrestamoBiblioteca extends EditRecord
{
    protected static string $resource = PrestamoBibliotecaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
