<?php

namespace App\Filament\Resources\Prestamos\Schemas;

use App\Models\Inventario;
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
use Spatie\Permission\Models\Role;

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
                                    ->searchable()
                                    ->preload()
                                    ->relationship(
                                        name: 'inventario',
                                        titleAttribute: 'nombre_equipo',
                                        modifyQueryUsing: fn($query, $get, $operation) =>
                                        $operation === 'create'
                                            ? $query->where('estado', 'disponible')->orderBy('nombre_equipo')
                                            : $query->orderBy('nombre_equipo')
                                    )
                                    ->getOptionLabelFromRecordUsing(
                                        fn(Inventario $record) =>
                                        "{$record->nombre_equipo}" .
                                            ($record->marca ? " — {$record->marca}" : "") .
                                            ($record->nro_serie ? " (S/N: {$record->nro_serie})" : "")
                                    )
                                    ->helperText('Solo se muestran equipos disponibles.')
                                    ->required()
                                    ->native(false)
                                    ->placeholder('Seleccionar equipo'),
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
                                        $action->visible(fn() => auth()->user()?->can('create_prestamo_biblioteca') || auth()->user()?->can('create_prestamo_informatica'));
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
                                    ->getOptionLabelFromRecordUsing(fn(User $u) => "{$u->name} ({$u->email})")
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
                    ])->columnSpanFull(),

                Section::make('Observaciones')
                    ->description('Notas internas o condiciones del préstamo.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Textarea::make('observaciones')
                            ->rows(3)
                            ->placeholder('Ej.: Entregado con cargador, bolsa y control remoto…')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
