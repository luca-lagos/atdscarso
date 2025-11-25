<?php

namespace App\Filament\Resources\TurnosSalas\Schemas;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TurnosSalaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del turno')
                    ->description('Asigná profesor, curso y horario para la sala.')
                    ->icon('heroicon-m-calendar-days')
                    ->schema([
                        // Fila 1: Profesor + Tipo + Estado
                        Grid::make()->columns([
                            'default' => 1,
                            'md' => 3,
                        ])->schema([
                            Select::make('user_id')
                                ->label('Usuario')
                                ->searchable()
                                ->preload()
                                ->options(
                                    User::query()
                                        ->whereHas('roles', fn($q) => $q->whereIn('name', ['profesor', 'alumno']))
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                )
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

                                    Select::make('role_id')
                                        ->label('Rol')
                                        ->options(
                                            Role::query()
                                                ->whereIn('name', ['profesor', 'alumno'])
                                                ->pluck('name', 'id')
                                        )
                                        ->required()
                                        ->native(false),
                                ])
                                ->createOptionAction(function (Action $action) {
                                    $action->visible(fn() => auth()->user()?->can('create_turnos_sala'));
                                    return $action
                                        ->modalHeading('Crear usuario (Profesor/Alumno)')
                                        ->modalWidth('md');
                                })
                                ->createOptionUsing(function (array $data): int {
                                    $role = Role::find($data['role_id']);

                                    $user = User::create([
                                        'name'     => $data['name'],
                                        'email'    => $data['email'],
                                        'password' => Hash::make($data['password']),
                                    ]);

                                    if ($role) {
                                        $user->assignRole($role->name);
                                    }

                                    return $user->getKey();
                                })
                                ->getOptionLabelFromRecordUsing(fn(User $u) => "{$u->name} ({$u->email})"),

                            Select::make('tipo')
                                ->label('Tipo')
                                ->options([
                                    'permanente' => 'Permanente',
                                    'temporal'   => 'Temporal',
                                ])
                                ->required()
                                ->native(false),

                            Select::make('estado')
                                ->label('Estado')
                                ->options([
                                    'activo'    => 'Activo',
                                    'pendiente' => 'Pendiente',
                                    'cancelado' => 'Cancelado',
                                ])
                                ->default(fn($get) => function () use ($get) {
                                    $userId = $get('user_id');
                                    if (! $userId) {
                                        return 'pendiente';
                                    }

                                    $user = User::find($userId);
                                    if (! $user) {
                                        return 'pendiente';
                                    }

                                    if ($user->hasRole('profesor')) {
                                        return 'activo';
                                    }

                                    if ($user->hasRole('alumno')) {
                                        return 'pendiente';
                                    }

                                    return 'pendiente';
                                })
                                ->native(false)
                                ->required(),
                        ]),

                        // Fila 2: Curso / División
                        Grid::make()->columns([
                            'default' => 1,
                            'md' => 2,
                        ])->schema([
                            TextInput::make('curso')
                                ->label('Curso')
                                ->placeholder('Ej.: 3°')
                                ->maxLength(50)
                                ->required(),

                            TextInput::make('division')
                                ->label('División')
                                ->placeholder('Ej.: B')
                                ->maxLength(50)
                                ->required(),
                        ]),

                        // Fila 3: Fecha
                        Grid::make()->columns(1)->schema([
                            DatePicker::make('fecha_turno')
                                ->label('Fecha')
                                ->required()
                                ->native(false)
                                ->minDate(fn($operation) => $operation === 'create' ? today() : null),
                        ]),

                        // Fila 4: Horario
                        Grid::make()->columns([
                            'default' => 1,
                            'md' => 2,
                        ])->schema([
                            TimePicker::make('hora_inicio')
                                ->label('Desde')
                                ->required()
                                ->native(false),

                            TimePicker::make('hora_fin')
                                ->label('Hasta')
                                ->required()
                                ->native(false)
                                ->rule(fn($get) => function (string $attribute, $value, $fail) use ($get) {
                                    $inicio = $get('hora_inicio');
                                    if ($inicio && $value && $value <= $inicio) {
                                        $fail('La hora de fin debe ser posterior a la hora de inicio.');
                                    }
                                }),
                        ]),
                    ])->columnSpanFull(),

                Section::make('Observaciones')
                    ->description('Notas internas o requerimientos para el turno.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Textarea::make('observaciones')
                            ->rows(3)
                            ->placeholder('Ej.: Necesita proyector, 25 PCs encendidas…')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
