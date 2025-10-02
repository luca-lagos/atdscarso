<?php

namespace App\Filament\Resources\TurnosTvs\Pages;

use App\Filament\Resources\TurnosTvs\TurnosTvResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTurnosTv extends EditRecord
{
    protected static string $resource = TurnosTvResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
