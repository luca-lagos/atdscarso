<?php

namespace App\Filament\Resources\Inventarios\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InventarioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles del Equipo')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nombre_equipo')
                            ->required()
                            ->label('Nombre del Equipo')
                            ->maxLength(150),
                        Select::make('categoria')
                            ->required()
                            ->label('Categoría')
                            ->options([
                                'notebook'     => 'Notebook',
                                'pc_escritorio'=> 'PC de escritorio',
                                'tv_portatil'  => 'TV portátil',
                                'proyector'    => 'Proyector',
                                'impresora'    => 'Impresora',
                                'otro'         => 'Otro',
                            ])
                            ->native(false)
                            ->searchable(),
                        TextInput::make('marca')
                        ->label('Marca')
                            ->maxLength(100),
                        TextInput::make('modelo')
                        ->label('Modelo')
                            ->maxLength(100),
                        TextInput::make('nro_serie')
                            ->required()
                            ->label('N° de Serie')
                            ->unique(ignoreRecord: true)
                            ->maxLength(120),
                        Select::make('estado')
                        ->label('Estado')
                            ->options([
                                'disponible' => 'Disponible',
                                'prestado' => 'Prestado',
                                'en_reparacion' => 'En reparacion',
                                'baja' => 'Baja',
                            ])
                            ->default('disponible')
                            ->required()
                            ->native(false),
                    ]),
                Section::make('Observaciones')
                    ->schema([
                        Textarea::make('observaciones')
                            ->columnSpanFull()
                            ->rows(4)
                            ->maxLength(2000)
                            ->label('Observaciones Adicionales'),
                    ]),
            ]);
    }
}
