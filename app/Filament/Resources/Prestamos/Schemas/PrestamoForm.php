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
                                    ->label('Profesor')
                                    ->options(
                                        User::query()
                                            ->where('rol', 'profesor')
                                            ->orderBy('name')
                                            ->pluck('name', 'id')
                                            ->toArray()
                                    )
                                    /*->relationship('user', 'nombre_completo', modifyQueryUsing: fn($q) => $q->where('rol', 'profesor'))*/
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->placeholder('Seleccionar profesor')
                                    ->required()
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
                                        'cerrado'  => 'Cerrado',
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
