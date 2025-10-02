<?php

namespace App\Filament\Resources\Inventarios\RelationManagers;

use App\Filament\Resources\Inventarios\InventarioResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PrestamosRelationManager extends RelationManager
{
    protected static string $relationship = 'Prestamos';
    
    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $relatedResource = InventarioResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
        ->components([
             Select::make('user_id')
             ->label('Profesor')
                ->relationship('user', 'nombre_completo')
                ->required()
                ->searchable()
                ->native(false),
            DatePicker::make('fecha_prestamo')
                ->label('Fecha de Préstamo')
                ->required()
                ->default(today()),
            DatePicker::make('fecha_devolucion')
                ->label('Fecha de Devolución'),
            Select::make('estado')
                ->label('Estado')
                ->options([
                    'activo'   => 'Activo',
                    'cerrado'  => 'Cerrado',
                    'vencido'  => 'Vencido',
                ])
                ->default('activo')
                ->required()
                ->native(false),
            Textarea::make('observaciones')
                ->label('Observaciones')
                ->rows(3)
                ->maxLength(500)
                ->columnSpan(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.nombre_completo')
                    ->label('Profesor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha_prestamo')
                    ->label('Fecha de Préstamo')
                    ->date()
                    ->sortable(),
                TextColumn::make('fecha_devolucion')
                    ->label('Fecha de Devolución')
                    ->date()
                    ->sortable(),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'success' => 'cerrado',
                        'warning' => 'activo',
                        'danger'  => 'vencido',
                    ])
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
