<?php

namespace App\Filament\Resources\TurnosTvs\Pages;

use App\Filament\Resources\TurnosTvs\TurnosTvResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTurnosTvs extends ListRecords
{
    protected static string $resource = TurnosTvResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
