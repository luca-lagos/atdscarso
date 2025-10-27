<?php

namespace App\Filament\Resources\Inventarios\Schemas;

use Filament\Forms\Components\Grid;
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
                Section::make('Detalles del equipo')
                    ->description('Completá la información principal del dispositivo. Los campos obligatorios aparecen marcados.')
                    ->icon('heroicon-m-computer-desktop')
                    ->columns(12)
                    ->schema([
                        TextInput::make('nombre_equipo')
                            ->label('Nombre del equipo')
                            ->placeholder('Ej.: Notebook laboratorio 3')
                            ->required()
                            ->maxLength(150)
                            ->columnSpan(6),

                        Select::make('categoria')
                            ->label('Categoría')
                            ->options([
                                'notebook'      => 'Notebook',
                                'pc_escritorio' => 'PC de escritorio',
                                'tv_portatil'   => 'TV portátil',
                                'proyector'     => 'Proyector',
                                'impresora'     => 'Impresora',
                                'otro'          => 'Otro',
                            ])
                            ->searchable()
                            ->native(false)
                            ->placeholder('Seleccionar categoría')
                            ->required()
                            ->columnSpan(6),

                        TextInput::make('marca')
                            ->label('Marca')
                            ->placeholder('Ej.: HP, Lenovo, Epson...')
                            ->maxLength(100)
                            ->columnSpan(4),

                        TextInput::make('modelo')
                            ->label('Modelo')
                            ->placeholder('Ej.: ProBook 440 G8')
                            ->maxLength(100)
                            ->columnSpan(4),

                        TextInput::make('nro_serie')
                            ->label('N° de serie')
                            ->placeholder('Ej.: SN-ABC123456')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(120)
                            ->helperText('Debe ser único. Verificá antes de guardar.')
                            ->columnSpan(4),

                        Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'disponible'    => 'Disponible',
                                'prestado'      => 'Prestado',
                                'en_reparacion' => 'En reparación',
                                'baja'          => 'Baja',
                            ])
                            ->default('disponible')
                            ->required()
                            ->native(false)
                            ->columnSpan(4),
                    ]),

                Section::make('Observaciones')
                    ->description('Notas internas, estado físico, accesorios, o advertencias.')
                    ->collapsible()
                    ->collapsed() // por defecto plegado
                    ->schema([
                        Textarea::make('observaciones')
                            ->label('Observaciones adicionales')
                            ->placeholder('Ej.: Faltan 2 teclas; con cargador; batería al 80%...')
                            ->rows(4)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
