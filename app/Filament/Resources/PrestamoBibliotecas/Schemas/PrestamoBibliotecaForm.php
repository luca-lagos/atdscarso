<?php

namespace App\Filament\Resources\PrestamoBibliotecas\Schemas;

use App\Models\InventarioBiblioteca;
use App\Models\PrestamoBiblioteca;
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
                                $fail('Este libro ya tiene un préstamo activo o vencido sin devolución.');
                            }
                        };
                    })
                    ->createOptionForm([
                        TextInput::make('titulo')->required()->maxLength(255),
                        TextInput::make('autor')->required()->maxLength(255),
                        TextInput::make('isbn')->label('ISBN')->maxLength(255),
                        TextInput::make('editorial')->maxLength(255),
                        TextInput::make('categoria')->maxLength(255),
                        TextInput::make('idioma')->maxLength(255),
                        TextInput::make('fecha_edicion')->numeric()->label('Año edición'),
                        Textarea::make('descripcion')->rows(3),
                        FileUpload::make('portada_path')
                            ->label('Portada')
                            ->image()
                            ->directory('portadas-libros')
                            ->imageEditor()
                            ->maxSize(2048),
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
