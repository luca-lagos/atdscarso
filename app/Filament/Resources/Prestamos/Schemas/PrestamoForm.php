<?php

namespace App\Filament\Resources\Prestamos\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PrestamoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('inventario_id')
                    ->required()
                    ->label('Equipo')
                    ->relationship('inventario', 'nombre_equipo')
                    ->searchable()
                    ->native(false),
                Select::make('user_id')
                    ->required()
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'nombre_completo',
                        modifyQueryUsing: fn ($query) => $query->where('role', 'profesor')
                    )
                    ->required()
                    ->searchable()
                    ->native(false),
                DatePicker::make('fecha_prestamo')
                    ->label('Fecha préstamo')
                    ->default(today())
                    ->required(),
                DatePicker::make('fecha_devolucion')
                ->label('Fecha devolución prevista')
                    ->nullable(),
                Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'activo'   => 'Activo',
                        'cerrado'  => 'Cerrado',
                        'vencido'  => 'Vencido',
                    ])
                    ->default('activo')
                    ->native(false),
                Textarea::make('observaciones')
                    ->rows(3),
            ]);
    }
}
