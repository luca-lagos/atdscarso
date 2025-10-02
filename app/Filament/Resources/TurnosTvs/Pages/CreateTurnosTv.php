<?php

namespace App\Filament\Resources\TurnosTvs\Pages;

use App\Filament\Resources\TurnosTvs\TurnosTvResource;
use App\Models\Turnos_tv;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateTurnosTv extends CreateRecord
{
    protected static string $resource = TurnosTvResource::class;

    protected function beforeCreate(): void
{
    $data = $this->form->getState();

    $conflict = Turnos_tv::where('inventario_id', $data['inventario_id'])
        ->where('fecha_turno', $data['fecha_turno'])
        ->where(function($q) use ($data) {
            $q->whereBetween('hora_inicio', [$data['hora_inicio'], $data['hora_fin']])
              ->orWhereBetween('hora_fin', [$data['hora_inicio'], $data['hora_fin']])
              ->orWhere(function($qq) use ($data) {
                  $qq->where('hora_inicio', '<=', $data['hora_inicio'])
                     ->where('hora_fin', '>=', $data['hora_fin']);
              });
        })
        ->exists();

    if ($conflict) {
        throw ValidationException::withMessages([
            'hora_inicio' => 'El TV ya está reservado en ese rango horario.',
            'hora_fin'    => 'El TV ya está reservado en ese rango horario.',
        ]);
    }
}
}
