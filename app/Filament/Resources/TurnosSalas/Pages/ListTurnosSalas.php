<?php

namespace App\Filament\Resources\TurnosSalas\Pages;

use App\Filament\Resources\TurnosSalas\TurnosSalaResource;
use App\Filament\Resources\TurnosSalas\Widgets\TurnosSalaCalendarWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTurnosSalas extends ListRecords
{
    protected static string $resource = TurnosSalaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TurnosSalaCalendarWidget::class,
        ];
    }
}
