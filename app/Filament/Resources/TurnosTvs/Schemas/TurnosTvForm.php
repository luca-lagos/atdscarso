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
                                ->label('Profesor')
                                ->options(
                                    User::query()
                                        ->where('rol', 'profesor') // o 'role'
                                        ->orderBy('name') // ajustá si usás 'name'
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
                                ->createOptionAction(function (Action $action) {
                                    $action->label('Crear profesor')->modalWidth('md');
                                }),

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
                            TimePicker::make('hora_inicio')
                                ->label('Desde')
                                ->required()
                                ->native(false),

                            TimePicker::make('hora_fin')
                                ->label('Hasta')
                                ->required()
                                ->native(false)
                                ->rule(fn(Closure $get) => function (string $attribute, $value, $fail) use ($get) {
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
                    ]),

                Section::make('Observaciones')
                    ->description('Notas o detalles para el turno.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Textarea::make('observaciones')
                            ->rows(3)
                            ->placeholder('Ej.: Se entrega con control y cable HDMI.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
