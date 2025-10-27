<?php

namespace App\Filament\Resources\Prestamos\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class PrestamoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del préstamo')
                    ->description('Seleccioná el equipo y el profesor, y definí las fechas.')
                    ->icon('heroicon-m-clipboard-document-check')
                    ->columns(12)
                    ->schema([
                        Select::make('inventario_id')
                            ->label('Equipo')
                            ->relationship(
                                name: 'inventario',
                                titleAttribute: 'nombre_equipo',
                                // Opcional: limitar a equipos disponibles cuando se crea
                                modifyQueryUsing: function (Builder $q) {
                                    // Descomentar si querés: $q->where('estado', 'disponible');
                                }
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->placeholder('Seleccionar equipo')
                            ->required()
                            ->columnSpan(6),

                        Select::make('user_id')
                            ->label('Profesor')
                            ->relationship(
                                name: 'user',
                                titleAttribute: 'nombre_completo',
                                modifyQueryUsing: fn(Builder $query) => $query->where('role', 'profesor')
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->placeholder('Seleccionar profesor')
                            ->required()
                            ->columnSpan(6),

                        Grid::make(12)->schema([
                            DatePicker::make('fecha_prestamo')
                                ->label('Fecha préstamo')
                                ->default(today())
                                ->required()
                                ->native(false)
                                ->columnSpan(6),

                            DatePicker::make('fecha_devolucion')
                                ->label('Fecha devolución prevista')
                                ->native(false)
                                ->after('fecha_prestamo') // no permite elegir anterior
                                ->placeholder('Opcional')
                                ->columnSpan(6),
                        ]),

                        Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'activo'   => 'Activo',
                                'cerrado'  => 'Cerrado',
                                'vencido'  => 'Vencido',
                            ])
                            ->default('activo')
                            ->native(false)
                            ->columnSpan(4),
                    ]),

                Section::make('Observaciones')
                    ->description('Notas internas o condiciones del préstamo.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Textarea::make('observaciones')
                            ->rows(3)
                            ->placeholder('Ej.: Entregado con cargador, bolsa y control remoto…')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
