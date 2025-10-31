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
use Filament\Schemas\Schema;

class PrestamoBibliotecaForm
{
    public static function configure(Schema $schema): Schema
    {
        $defaultVenc = fn() => Carbon::today()->addDays(14)->toDateString();

        return $schema
            ->components([
                Select::make('inventario_biblioteca_id')
                    ->label('Libro')
                    ->searchable()
                    ->preload()
                    ->options(fn() => InventarioBiblioteca::query()
                        ->orderBy('titulo')
                        ->pluck('titulo', 'id'))
                    ->getOptionLabelFromRecordUsing(fn(InventarioBiblioteca $r) => "{$r->titulo} — {$r->autor}")
                    ->required()
                    ->reactive()
                    ->rule(function () {
                        return function (string $attribute, $value, \Closure $fail) {
                            if (!$value) return;

                            $ocupado = PrestamoBiblioteca::query()
                                ->where('inventario_biblioteca_id', $value)
                                ->whereIn('estado', ['activo', 'vencido'])
                                ->whereNull('fecha_devolucion')
                                ->exists();

                            if ($ocupado) {
                                $fail('No hay ejemplares disponibles para préstamo.');
                            }
                        };
                    })
                    ->createOptionForm([
                        TextInput::make('titulo')->required()->columnSpan(8),
                        TextInput::make('isbn')->label('ISBN')->columnSpan(4),
                        TextInput::make('autor')->required()->columnSpan(6),
                        TextInput::make('editorial')->columnSpan(6),
                        Select::make('categoria')
                            ->options(self::categorias())
                            ->searchable()
                            ->placeholder('Seleccionar categoría')
                            ->columnSpan(4),
                        TextInput::make('idioma')->columnSpan(4),
                        TextInput::make('procedencia')->columnSpan(4),
                        TextInput::make('coleccion')->label('Colección')->columnSpan(4),
                        TextInput::make('numero_edicion')->label('N° edición')->columnSpan(4),
                        TextInput::make('cantidad')->numeric()->minValue(0)->default(1)->columnSpan(4),
                        TextInput::make('fecha_edicion')->numeric()->label('Año edición')->columnSpan(3),
                        TextInput::make('fecha_entrada')->numeric()->label('Año entrada')->columnSpan(3),
                        FileUpload::make('portada_path')
                            ->label('Portada')
                            ->image()
                            ->directory('portadas-libros')
                            ->imageEditor()
                            ->columnSpan(6),
                        Textarea::make('descripcion')->rows(4)->columnSpan(12),
                    ])
                    ->createOptionAction(function (Action $action) {
                        return $action
                            ->modalHeading('Nuevo libro')
                            ->modalButton('Crear y seleccionar')
                            ->mutateFormDataUsing(function (array $data) {
                                // Sanitizar/normalizar si querés
                                return $data;
                            });
                    })
                    ->createOptionUsing(function (array $data) {
                        // Crear y devolver el ID para que quede seleccionado
                        return InventarioBiblioteca::create($data)->getKey();
                    }),
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
                DatePicker::make('fecha_prestamo')
                    ->default(now()->toDateString())
                    ->required(),

                DatePicker::make('fecha_vencimiento')
                    ->default($defaultVenc)
                    ->required(),

                Textarea::make('observaciones')
                    ->rows(3)
                    ->columnSpanFull(),
            ])->columns(2);
    }
}
