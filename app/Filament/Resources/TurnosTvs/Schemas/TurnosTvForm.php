<?php

namespace App\Filament\Resources\TurnosTvs\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                ->native(false)
                ->createOptionForm([
                    TextInput::make('name')
                        ->label('Nombre completo')
                        ->maxLength(255)
                        ->required(),
                    TextInput::make('email')
                        ->label('Correo electrónico')
                        ->unique(ignoreRecord: true)
                        ->email()
                        ->required(),
                    TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                        ->dehydrated(fn($state) => filled($state))
                        ->label('Contraseña')
                        ->required(),
                    Select::make('rol')
                        ->options(['admin' => 'Admin', 'profesor' => 'Profesor'])
                        ->default('profesor')
                        ->label('Asignar rol')
                        ->required(),
                ])
                ->createOptionAction(
                    function(Action $action) {
                        $action
                            ->label('Crear profesor')
                            ->modalWidth('md');
                    }
                ),
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
