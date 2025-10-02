<?php

namespace App\Filament\Resources\Inventarios\RelationManagers;

use App\Filament\Resources\Inventarios\InventarioResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TurnosTvRelationManager extends RelationManager
{
    protected static string $relationship = 'turnosTv';

    protected static ?string $relatedResource = InventarioResource::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $title = 'Turnos TV';

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

            DatePicker::make('fecha_turno')
                ->label('Fecha')
                ->required(),

            TimePicker::make('hora_inicio')
                ->label('Hora inicio')
                ->required(),

            TimePicker::make('hora_fin')
                ->label('Hora fin')
                ->required(),
Select::make('estado')
                ->options([
                    'activo'     => 'Activo',
                    'confirmado' => 'Confirmado',
                    'cancelado'  => 'Cancelado',
                    'finalizado' => 'Finalizado',
                ])
                ->default('activo')
                ->native(false),

            Textarea::make('observaciones')
                ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('user.nombre_completo')
                    ->label('Profesor')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('fecha_turno')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('hora_inicio')
                    ->time('H:i'),

                TextColumn::make('hora_fin')
                    ->time('H:i'),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->colors([
                        'success' => 'activo',
                        'warning' => 'confirmado',
                        'gray'    => 'finalizado',
                        'danger'  => 'cancelado',
                    ]),
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
