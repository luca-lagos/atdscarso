<?php

namespace App\Filament\Resources\PrestamoBibliotecas\Schemas;

use App\Models\InventarioBiblioteca;
use App\Models\PrestamoBiblioteca;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class PrestamoBibliotecaForm
{
    private static function categorias(): array
    {
        return [
            'Administracion publica' => 'Administración pública',
            'Ciencias Naturales' => 'Ciencias Naturales',
            'Ciencias Sociales' => 'Ciencias Sociales',
            'Comic' => 'Cómic',
            'Dibujo' => 'Dibujo',
            'Diccionario' => 'Diccionario',
            'Educacion fisica' => 'Educación física',
            'Enciclopedia' => 'Enciclopedia',
            'Etica' => 'Ética',
            'Fisica' => 'Física',
            'Geografia' => 'Geografía',
            'Historia' => 'Historia',
            'Informatica' => 'Informática',
            'Lengua' => 'Lengua',
            'Matematica' => 'Matemática',
            'Musica' => 'Música',
            'Ocio' => 'Ocio',
            'Pedagogia' => 'Pedagogía',
            'Otros' => 'Otros',
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Libro')
                    ->schema([
                        Select::make('inventario_biblioteca_id')
                            ->label('Libro')
                            ->searchable()
                            ->preload()
                            ->relationship(
                                name: 'libro',
                                titleAttribute: 'titulo',
                                modifyQueryUsing: fn($query, $get, $operation) =>
                                $operation === 'create'
                                    ? $query->where('estado', '!=', 'prestado')->whereDoesntHave('prestamos', function ($q) {
                                        $q->whereIn('estado', ['pendiente', 'activo', 'vencido'])
                                            ->whereNull('fecha_devolucion');
                                    })->orderBy('titulo')
                                    : $query->orderBy('titulo')
                            )
                            ->getOptionLabelFromRecordUsing(function (InventarioBiblioteca $record) {
                                return "{$record->titulo} — {$record->autor}" .
                                    ($record->isbn ? " (ISBN: {$record->isbn})" : "");
                            })
                            ->helperText('Solo se muestran libros disponibles. Si no existe, créalo desde aquí.')
                            ->required()
                            ->createOptionForm([
                                TextInput::make('titulo')
                                    ->label('Título')
                                    ->required()
                                    ->columnSpan(8),

                                TextInput::make('isbn')
                                    ->label('ISBN')
                                    ->unique(table: InventarioBiblioteca::class, ignoreRecord: true)
                                    ->helperText('Debe ser único para cada ejemplar.')
                                    ->columnSpan(4),

                                TextInput::make('autor')
                                    ->required()
                                    ->columnSpan(6),

                                TextInput::make('editorial')
                                    ->columnSpan(6),

                                Select::make('categoria')
                                    ->options(self::categorias())
                                    ->searchable()
                                    ->placeholder('Seleccionar categoría')
                                    ->columnSpan(4),

                                TextInput::make('idioma')
                                    ->columnSpan(4),

                                TextInput::make('procedencia')
                                    ->columnSpan(4),

                                TextInput::make('coleccion')
                                    ->label('Colección')
                                    ->columnSpan(4),

                                TextInput::make('numero_edicion')
                                    ->label('N° edición')
                                    ->columnSpan(4),

                                TextInput::make('ubicacion_estante')
                                    ->label('Estante')
                                    ->placeholder('Ej.: A, B, C...')
                                    ->columnSpan(3),

                                TextInput::make('ubicacion_columna')
                                    ->label('Columna')
                                    ->placeholder('Ej.: 1, 2, 3...')
                                    ->columnSpan(3),

                                TextInput::make('fecha_edicion')
                                    ->numeric()
                                    ->label('Año edición')
                                    ->columnSpan(3),

                                TextInput::make('fecha_entrada')
                                    ->numeric()
                                    ->label('Año entrada')
                                    ->columnSpan(3),

                                FileUpload::make('portada_path')
                                    ->label('Portada')
                                    ->image()
                                    ->directory('portadas-libros')
                                    ->imageEditor()
                                    ->columnSpan(6),

                                Textarea::make('descripcion')
                                    ->rows(4)
                                    ->columnSpan(12),
                            ])
                            ->createOptionAction(function (Action $action) {
                                return $action
                                    ->modalHeading('Nuevo libro')
                                    ->modalButton('Crear y seleccionar')
                                    ->modalWidth('4xl');
                            })
                            ->createOptionUsing(function (array $data) {
                                $data['estado'] = 'disponible'; // Por defecto disponible
                                return InventarioBiblioteca::create($data)->getKey();
                            }),
                    ])->columnSpanFull(),

                Section::make('Usuario')
                    ->schema([
                        Select::make('user_id')
                            ->label('Usuario')
                            ->searchable()
                            ->preload()
                            ->options(User::query()->orderBy('name')->pluck('name', 'id'))
                            ->required()
                            ->helperText('Alumnos quedan Pendientes; Docentes quedan Activos.')
                            ->getOptionLabelFromRecordUsing(fn(User $u) => "{$u->name} ({$u->email})")
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
                                $action->visible(fn() => auth()->user()?->can('create_prestamo_biblioteca') || auth()->user()?->can('create_prestamo'));
                                return $action
                                    ->modalHeading('Crear usuario (Profesor/Alumno)')
                                    ->modalWidth('md');
                            })
                            ->createOptionUsing(function (array $data): int {
                                $rol = in_array($data['rol'] ?? '', ['profesor', 'alumno'], true) ? $data['rol'] : 'alumno';
                                $user = User::create([
                                    'name'     => $data['name'],
                                    'email'    => $data['email'],
                                    'password' => Hash::make($data['password']),
                                ]);
                                $user->assignRole($rol);
                                return $user->getKey();
                            }),
                    ])->columnSpanFull(),

                Section::make('Fechas y notas')
                    ->schema([
                        Grid::make(12)->schema([
                            DatePicker::make('fecha_prestamo')
                                ->label('Inicio')
                                ->default(now()->toDateString())
                                ->required()
                                ->columnSpan(6)
                                ->native(false),

                            DatePicker::make('fecha_vencimiento')
                                ->label('Vence')
                                ->default(fn() => Carbon::today()->addDays(14)->toDateString())
                                ->required()
                                ->columnSpan(6)
                                ->native(false),
                        ]),

                        Textarea::make('observaciones')
                            ->rows(3)
                            ->placeholder('Notas internas, estado del ejemplar, etc.')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ])
            ->columns(2);
    }
}
