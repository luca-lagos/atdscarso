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
                        TextInput::make('titulo')
                            ->label('Título')
                            ->required()
                            ->placeholder('Ej.: El Principito')
                            ->columnSpan(8),

                        TextInput::make('isbn')
                            ->label('ISBN')
                            ->placeholder('978-...')
                            ->columnSpan(4),

                        TextInput::make('autor')
                            ->required()
                            ->placeholder('Nombre Apellido')
                            ->columnSpan(6),

                        TextInput::make('editorial')
                            ->placeholder('Ej.: Kapelusz')
                            ->columnSpan(6),

                        Select::make('categoria')
                            ->options(self::categorias())
                            ->searchable()
                            ->placeholder('Seleccionar categoría')
                            ->native(false)
                            ->columnSpan(4),

                        TextInput::make('idioma')
                            ->placeholder('Español')
                            ->columnSpan(4),

                        TextInput::make('procedencia')
                            ->placeholder('Compra / Donación / Otro')
                            ->columnSpan(4),

                        TextInput::make('coleccion')
                            ->label('Colección')
                            ->placeholder('Ej.: Clásicos')
                            ->columnSpan(4),

                        TextInput::make('numero_edicion')
                            ->label('N° edición')
                            ->placeholder('1, 2, 3...')
                            ->columnSpan(4),

                        TextInput::make('cantidad')
                            ->numeric()
                            ->minValue(0)
                            ->default(1)
                            ->helperText('Cantidad total de ejemplares.')
                            ->columnSpan(4),

                        TextInput::make('fecha_edicion')
                            ->label('Año edición')
                            ->numeric()
                            ->minValue(1800)
                            ->maxValue((int) now()->year)
                            ->placeholder((string) now()->year)
                            ->columnSpan(3),

                        TextInput::make('fecha_entrada')
                            ->label('Año entrada')
                            ->numeric()
                            ->minValue(1990)
                            ->maxValue((int) now()->year)
                            ->placeholder((string) now()->year)
                            ->columnSpan(3),

                        FileUpload::make('portada_path')
                            ->label('Portada')
                            ->image()
                            ->directory('portadas-libros')
                            ->imageEditor()
                            ->imagePreviewHeight('220')
                            ->panelAspectRatio('3:4')
                            ->panelLayout('integrated')
                            ->columnSpan(6),

                        Textarea::make('descripcion')
                            ->rows(4)
                            ->placeholder('Resumen breve, notas, estado físico, etc.')
                            ->columnSpan(12),
                    ])
                ])->columns(12),
            ]);
    }
}
