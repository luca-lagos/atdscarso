<?php

namespace App\Filament\Resources\PrestamoBibliotecas\Schemas;

use App\Models\InventarioBiblioteca;
use Carbon\Carbon;
use Filament\Schemas\Schema;

class PrestamoBibliotecaForm
{
    public static function configure(Schema $schema): Schema
    {
        $defaultVenc = fn() => Carbon::today()->addDays(14)->toDateString();

        return $schema
            ->components([
                Forms\Components\Select::make('inventario_biblioteca_id')
                    ->label('Libro')
                    ->searchable()
                    ->preload()
                    ->options(fn() => InventarioBiblioteca::query()
                        ->orderBy('titulo')
                        ->pluck('titulo', 'id'))
                    ->getOptionLabelFromRecordUsing(fn(InventarioBiblioteca $r) => "{$r->titulo} — {$r->autor}")
                    ->required()
                    ->reactive()
                    ->helperText('Solo cree el préstamo si el libro está disponible.')
                    ->rule('different:libro_no_disponible'), // visual: no bloquea, validamos abajo
                Forms\Components\DatePicker::make('fecha_prestamo')
                    ->default(now()->toDateString())
                    ->required(),
                Forms\Components\DatePicker::make('fecha_vencimiento')
                    ->default($defaultVenc)
                    ->required(),
                Forms\Components\Textarea::make('observaciones')->rows(3)->columnSpanFull(),
            ])->columns(2)
            ->afterStateHydrated(function ($form) {
                // nada especial
            })
            ->rules([
                'inventario_biblioteca_id' => function ($attribute, $value, $fail) {
                    if (!$value) return;
                    $ocupado = \App\Models\PrestamoBiblioteca::query()
                        ->where('inventario_biblioteca_id', $value)
                        ->whereIn('estado', ['activo', 'vencido'])
                        ->whereNull('fecha_devolucion')
                        ->exists();
                    if ($ocupado) {
                        $fail('Este libro ya tiene un préstamo activo o vencido sin devolución.');
                    }
                },
            ]);
    }
}
