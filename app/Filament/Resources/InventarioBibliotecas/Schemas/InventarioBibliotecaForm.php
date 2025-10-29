<?php

namespace App\Filament\Resources\InventarioBibliotecas\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InventarioBibliotecaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del libro')->schema([
                    Grid::make(12)->schema([
                        Forms\Components\TextInput::make('titulo')->required()->columnSpan(8),
                        Forms\Components\TextInput::make('isbn')->label('ISBN')->columnSpan(4),
                        Forms\Components\TextInput::make('autor')->required()->columnSpan(6),
                        Forms\Components\TextInput::make('editorial')->columnSpan(6),
                        Forms\Components\TextInput::make('categoria')->columnSpan(4),
                        Forms\Components\TextInput::make('idioma')->columnSpan(4),
                        Forms\Components\TextInput::make('procedencia')->columnSpan(4),
                        Forms\Components\TextInput::make('fecha_edicion')->numeric()->label('Año edición')->columnSpan(3),
                        Forms\Components\TextInput::make('fecha_entrada')->numeric()->label('Año entrada')->columnSpan(3),
                        Forms\Components\FileUpload::make('portada_path')
                            ->label('Portada')
                            ->image()
                            ->directory('portadas-libros')
                            ->imageEditor()
                            ->columnSpan(6),
                        Forms\Components\Textarea::make('descripcion')->rows(4)->columnSpan(12),
                    ]),
                ])->columns(12),
            ]);
    }
}
