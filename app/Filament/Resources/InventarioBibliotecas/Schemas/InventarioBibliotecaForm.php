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
                Section::make('Datos del libro')
                    ->description('Completa la información principal y, si es posible, sube la portada.')
                    ->schema([
                        Grid::make(12)->schema([
                            // Portada grande a la izquierda (en desktop)
                            FileUpload::make('portada_path')
                                ->label('Portada')
                                ->image()
                                ->directory('portadas-libros')
                                ->imageEditor()
                                ->imagePreviewHeight('260')
                                ->panelAspectRatio('3:4')
                                ->panelLayout('integrated')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->helperText('Formatos: JPG/PNG/WebP.')
                                ->columnSpan([
                                    'default' => 12,
                                    'lg'      => 3,
                                ]),

                            Section::make('Identificación')
                                ->collapsible()
                                ->schema([
                                    TextInput::make('titulo')
                                        ->label('Título')
                                        ->required()
                                        ->placeholder('Ej.: El Principito')
                                        ->hintIcon('heroicon-o-information-circle', tooltip: 'Nombre del libro tal como figura en tapa.')
                                        ->maxLength(255),
                                    Grid::make(12)->schema([
                                        TextInput::make('autor')
                                            ->required()
                                            ->placeholder('Nombre Apellido')
                                            ->maxLength(255)
                                            ->columnSpan(8),
                                        TextInput::make('isbn')
                                            ->label('ISBN')
                                            ->placeholder('978-...')
                                            ->maxLength(50)
                                            ->columnSpan(4),
                                    ]),
                                    Grid::make(12)->schema([
                                        TextInput::make('editorial')
                                            ->placeholder('Ej.: Kapelusz')
                                            ->maxLength(120)
                                            ->columnSpan(6),
                                        Select::make('categoria')
                                            ->options(self::categorias())
                                            ->searchable()
                                            ->placeholder('Seleccionar categoría')
                                            ->native(false)
                                            ->columnSpan(6),
                                    ]),
                                    Grid::make(12)->schema([
                                        TextInput::make('estante')
                                            ->placeholder('Ej.: A, B, C...')
                                            ->maxLength(50)
                                            ->columnSpan(6),

                                        TextInput::make('columna')
                                            ->placeholder('Ej.: 1, 2, 3...')
                                            ->maxLength(50)
                                            ->columnSpan(6),
                                    ]),
                                ])
                                ->columnSpan([
                                    'default' => 12,
                                    'lg'      => 9,
                                ]),

                            Section::make('Detalles')
                                ->collapsible()
                                ->schema([
                                    Grid::make(12)->schema([
                                        TextInput::make('idioma')
                                            ->placeholder('Español')
                                            ->maxLength(80)
                                            ->columnSpan(4),

                                        TextInput::make('procedencia')
                                            ->placeholder('Compra / Donación / Otro')
                                            ->maxLength(120)
                                            ->columnSpan(4),

                                        TextInput::make('coleccion')
                                            ->label('Colección')
                                            ->placeholder('Ej.: Clásicos')
                                            ->maxLength(120)
                                            ->columnSpan(4),

                                        TextInput::make('numero_edicion')
                                            ->label('N° edición')
                                            ->placeholder('1, 2, 3...')
                                            ->numeric()
                                            ->minValue(1)
                                            ->columnSpan(4),

                                        Select::make('estado')
                                            ->label('Estado')
                                            ->options([
                                                'disponible' => 'Disponible',
                                                'prestado' => 'Prestado',
                                                'en_reparacion' => 'En reparación',
                                                'extraviado' => 'Extraviado',
                                                'baja' => 'Baja',
                                            ])
                                            ->default('disponible')
                                            ->required()
                                            ->native(false)
                                            ->columnSpan(4),

                                        TextInput::make('fecha_edicion')
                                            ->label('Año edición')
                                            ->numeric()
                                            ->minValue(1800)
                                            ->maxValue((int) now()->year)
                                            ->placeholder((string) now()->year)
                                            ->columnSpan(2),

                                        TextInput::make('fecha_entrada')
                                            ->label('Año entrada')
                                            ->numeric()
                                            ->minValue(1990)
                                            ->maxValue((int) now()->year)
                                            ->placeholder((string) now()->year)
                                            ->columnSpan(2),
                                    ]),

                                    Textarea::make('descripcion')
                                        ->rows(4)
                                        ->placeholder('Resumen breve, notas, estado físico, dedicatorias, etc.')
                                        ->columnSpan(12),
                                ])
                                ->columnSpanFull(),
                        ]),
                    ])
                    ->columnSpanFull()
            ]);
    }
}
