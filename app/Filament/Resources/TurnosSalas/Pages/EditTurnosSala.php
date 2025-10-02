<?php

namespace App\Filament\Resources\TurnosSalas\Pages;

use App\Filament\Resources\TurnosSalas\TurnosSalaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTurnosSala extends EditRecord
{
    protected static string $resource = TurnosSalaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
