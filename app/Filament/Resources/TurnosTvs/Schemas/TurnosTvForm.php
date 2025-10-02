<?php

namespace App\Filament\Resources\TurnosTvs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class TurnosTvForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Profesor')
                ->relationship(
                    name: 'user',
                    titleAttribute: 'nombre_completo',
                    modifyQueryUsing: fn ($q) => $q->where('rol', 'profesor')
                )
                ->required()
                ->searchable()
                ->native(false),
                Select::make('inventario_id')
                    ->label('TV portátil')
                ->relationship(
                    name: 'inventario',
                    titleAttribute: 'nombre_equipo',
                    modifyQueryUsing: fn ($q) => $q->where('categoria', 'tv_portatil')
                )
                ->required()
                ->searchable()
                ->native(false),
            DatePicker::make('fecha_turno')
                ->label('Fecha')
                ->required(),
            TimePicker::make('hora_inicio')
                ->required()
                ->label('Desde'),
                TimePicker::make('hora_fin')
                ->required()
                ->label('Hasta'),
           Select::make('estado')
                ->options([
                    'activo'     => 'Activo',
                    'confirmado' => 'Confirmado',
                    'cancelado'  => 'Cancelado',
                    'finalizado' => 'Finalizado',
                ])
                ->default('activo')
                ->native(false),
            Textarea::make('observaciones')->rows(3),
            ]);
    }
}
