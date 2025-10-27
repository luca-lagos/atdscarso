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
use Illuminate\Database\Eloquent\Builder;
use Closure;

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
                        // Fila 1: Profesor + Tipo
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

                            Select::make('tipo')
                                ->label('Tipo')
                                ->options([
                                    'permanente' => 'Permanente',
                                    'temporal'   => 'Temporal',
                                ])
                                ->required()
                                ->native(false),
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
                                ->minDate(today()), // evita fechas pasadas si querés
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
                                ->rule(fn(Closure $get) => function (string $attribute, $value, $fail) use ($get) {
                                    $inicio = $get('hora_inicio');
                                    if ($inicio && $value && $value <= $inicio) {
                                        $fail('La hora de fin debe ser posterior a la hora de inicio.');
                                    }
                                }),
                        ]),
                    ]),

                Section::make('Observaciones')
                    ->description('Notas internas o requerimientos para el turno.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Textarea::make('observaciones')
                            ->rows(3)
                            ->placeholder('Ej.: Necesita proyector, 25 PCs encendidas…')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
