<?php

namespace App\Filament\Resources\TurnosSalas\Pages;

use App\Filament\Resources\TurnosSalas\TurnosSalaResource;
use App\Models\Turnos_sala;
use Filament\Resources\Pages\CreateRecord;

class CreateTurnosSala extends CreateRecord
{
    protected static string $resource = TurnosSalaResource::class;

    protected function beforeCreate(): void
    {
        $data = $this->form->getState();

        $conflict = Turnos_sala::where('fecha_turno', $data['fecha_turno'])
            ->where(function ($q) use ($data) {
                $q->whereBetween('hora_inicio', [$data['hora_inicio'], $data['hora_fin']])
                    ->orWhereBetween('hora_fin', [$data['hora_inicio'], $data['hora_fin']])
                    ->orWhere(function ($qq) use ($data) {
                        $qq->where('hora_inicio', '<=', $data['hora_inicio'])
                            ->where('hora_fin', '>=', $data['hora_fin']);
                    });
            })
            ->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'hora_inicio' => 'La sala ya está reservada en ese rango horario.',
                'hora_fin'    => 'La sala ya está reservada en ese rango horario.',
            ]);
        }
    }
}
