<?php

namespace App\Filament\Resources\TurnosSalas\Schemas;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class TurnosSalaForm
{

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Profesor')
                    ->options(
                        User::query()
                            ->where('rol', 'profesor')  // o ->role('profesor') con Spatie
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->required()
                    ->searchable()
                    ->preload()
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
                        function (Action $action) {
                            $action
                                ->label('Crear profesor')
                                ->modalWidth('md');
                        }
                    ),
                TextInput::make('curso')
                    ->label('Curso')
                    ->maxLength(50)
                    ->required(),
                TextInput::make('division')
                    ->label('División')
                    ->maxLength(50)
                    ->required(),
                Select::make('tipo')
                    ->label('Tipo')
                    ->options(
                        [
                            'permanente' => 'Permanente',
                            'temporal' => 'Temporal'
                        ]
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->native(false),
                DatePicker::make('fecha_turno')
                    ->label('Fecha')
                    ->required()
                    ->native(false),
                TimePicker::make('hora_inicio')
                    ->required()
                    ->label('Desde')
                    ->native(false),
                TimePicker::make('hora_fin')
                    ->required()
                    ->label('Hasta')
                    ->native(false),
                Textarea::make('observaciones')->rows(3),
            ]);
    }
}
