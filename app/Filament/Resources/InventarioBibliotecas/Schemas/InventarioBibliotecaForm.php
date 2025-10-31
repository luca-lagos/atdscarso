<?php

namespace App\Filament\Resources\InventarioBibliotecas\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InventarioBibliotecaForm
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
                Section::make('Datos del libro')->schema([
                    Grid::make(12)->schema([
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
                    ]),
                ])->columns(12),
            ]);
    }
}
