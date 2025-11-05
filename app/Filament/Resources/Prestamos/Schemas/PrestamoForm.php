<?php

namespace App\Filament\Resources\Prestamos\Schemas;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
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
                    ->schema([
                        // Fila 1: Equipo / Profesor
                        Grid::make()
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                            ])
                            ->schema([
                                Select::make('inventario_id')
                                    ->label('Equipo')
                                    ->relationship('inventario', 'nombre_equipo')
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->placeholder('Seleccionar equipo')
                                    ->required(),

                                Select::make('user_id')
                                    ->label('Usuario')
                                    ->searchable()
                                    ->preload()
                                    ->options(User::query()->orderBy('name')->pluck('name', 'id'))
                                    ->required()
                                    ->helperText('Alumnos quedan Pendientes hasta confirmación; Docentes quedan Activos.')
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Nombre y apellido')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('email')
                                            ->label('Email')
                                            ->required()
                                            ->email()
                                            ->unique(ignoreRecord: true),

                                        TextInput::make('password')
                                            ->label('Contraseña')
                                            ->password()
                                            ->required()
                                            ->minLength(6),

                                        Select::make('rol')
                                            ->label('Rol')
                                            ->options([
                                                'profesor' => 'Profesor',
                                                'alumno'   => 'Alumno',
                                            ])
                                            ->required()
                                            ->native(false),
                                    ])
                                    ->createOptionAction(function (Action $action) {
                                        // Solo quienes pueden crear préstamos pueden usar esta acción.
                                        $action->visible(fn() => auth()->user()?->can('create_prestamo_biblioteca') || auth()->user()?->can('create_prestamo_informatica'));
                                        return $action
                                            ->modalHeading('Crear usuario (Profesor/Alumno)')
                                            ->modalWidth('md');
                                    })
                                    ->createOptionUsing(function (array $data): int {
                                        // Seguridad: forzar rol permitido
                                        $rol = in_array($data['rol'] ?? '', ['profesor', 'alumno'], true) ? $data['rol'] : 'alumno';

                                        $user = User::create([
                                            'name'     => $data['name'],
                                            'email'    => $data['email'],
                                            'password' => Hash::make($data['password']),
                                        ]);

                                        // Asignar rol
                                        $user->assignRole($rol);

                                        return $user->getKey();
                                    })
                                    ->getOptionLabelFromRecordUsing(fn(User $u) => "{$u->name} ({$u->email})"),
                            ]),

                        // Fila 2: Fechas (6/6 en md+)
                        Grid::make()
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                            ])
                            ->schema([
                                DatePicker::make('fecha_prestamo')
                                    ->label('Fecha préstamo')
                                    ->default(today())
                                    ->required()
                                    ->native(false),

                                DatePicker::make('fecha_devolucion')
                                    ->label('Fecha devolución prevista')
                                    ->native(false)
                                    ->after('fecha_prestamo')
                                    ->placeholder('Opcional'),
                            ]),

                        // Fila 3: Estado (fila propia para evitar solape)
                        Grid::make()
                            ->columns(1)
                            ->schema([
                                Select::make('estado')
                                    ->label('Estado')
                                    ->options([
                                        'activo'   => 'Activo',
                                        'devuelto'  => 'Devuelto',
                                        'vencido'  => 'Vencido',
                                    ])
                                    ->default('activo')
                                    ->native(false),
                            ]),
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
