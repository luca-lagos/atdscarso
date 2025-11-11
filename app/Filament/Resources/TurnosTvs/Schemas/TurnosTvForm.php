<?php

namespace App\Filament\Resources\TurnosTvs\Schemas;

use App\Models\Inventario;
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
use Closure;
use HusamTariq\FilamentTimePicker\Forms\Components\TimePickerField;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TurnosTvForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del turno de TV')
                    ->description('Asigná profesor, TV y horario.')
                    ->icon('heroicon-m-tv')
                    ->schema([
                        // Fila 1: Profesor + TV
                        Grid::make()->columns([
                            'default' => 1,
                            'md' => 2,
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

                                    // ✅ Ahora el rol viene desde la tabla "roles"
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
                                    $action->visible(fn() => auth()->user()?->can('create_turnos_tv'));
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

                            // TV portátil: sólo inventarios con categoría tv_portatil
                            Select::make('inventario_id')
                                ->label('TV portátil')
                                ->options(
                                    Inventario::query()
                                        ->where('categoria', 'tv_portatil')
                                        ->orderBy('nombre_equipo')
                                        ->pluck('nombre_equipo', 'id')
                                        ->toArray()
                                )
                                ->required()
                                ->searchable()
                                ->preload()
                                ->native(false),
                        ]),

                        // Fila 2: Fecha
                        Grid::make()->columns(1)->schema([
                            DatePicker::make('fecha_turno')
                                ->label('Fecha')
                                ->required()
                                ->native(false)
                                ->minDate(today()), // opcional: evitar fechas pasadas
                        ]),

                        // Fila 3: Horario
                        Grid::make()->columns([
                            'default' => 1,
                            'md' => 2,
                        ])->schema([
                            /*TimePicker::make('hora_inicio')
                                ->label('Desde')
                                ->required()
                                ->native(false),*/

                            TimePickerField::make('hora_inicio')
                                ->label('Desde')
                                ->required()
                                ->okLabel('Confirmar')
                                ->cancelLabel('Cancelar'),

                            TimePickerField::make('hora_fin')
                                ->label('Hasta')
                                ->required()
                                ->okLabel('Confirmar')
                                ->cancelLabel('Cancelar')
                                ->rule(fn($get) => function (string $attribute, $value, $fail) use ($get) {
                                    $inicio = $get('hora_inicio');
                                    if ($inicio && $value && $value <= $inicio) {
                                        $fail('La hora de fin debe ser posterior a la hora de inicio.');
                                    }
                                }),
                        ]),

                        // Fila 4: Estado
                        Grid::make()->columns(1)->schema([
                            Select::make('estado')
                                ->label('Estado')
                                ->options([
                                    'activo'     => 'Activo',
                                    'confirmado' => 'Confirmado',
                                    'cancelado'  => 'Cancelado',
                                    'finalizado' => 'Finalizado',
                                ])
                                ->default('activo')
                                ->native(false),
                        ]),
                    ])->columnSpanFull(),

                Section::make('Observaciones')
                    ->description('Notas o detalles para el turno.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Textarea::make('observaciones')
                            ->rows(3)
                            ->placeholder('Ej.: Se entrega con control y cable HDMI.')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
