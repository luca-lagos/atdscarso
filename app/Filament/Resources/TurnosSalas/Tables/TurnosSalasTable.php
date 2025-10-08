<?php

namespace App\Filament\Resources\TurnosSalas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TurnosSalasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.nombre_completo')->label('Profesor')
                    ->sortable()->searchable(),
                TextColumn::make('curso')->sortable()->searchable(),
                TextColumn::make('division')->sortable()->searchable(),
                TextColumn::make('fecha_turno')->label('Fecha')->date('d/m/Y')->sortable(),
                TextColumn::make('hora_inicio')->label('Inicio')->time('H:i')->sortable(),
                TextColumn::make('hora_fin')->label('Fin')->time('H:i')->sortable(),
                TextColumn::make('tipo')->badge()->toggleable(),
                TextColumn::make('created_at')->dateTime('d/m/Y H:i')->label('Creado')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('hoy')
                    ->query(fn(Builder $q) => $q->whereDate('fecha_turno', today()))
                    ->label('Hoy'),
                Filter::make('semana')
                    ->query(fn(Builder $q) => $q->whereBetween('fecha_turno', [now()->startOfWeek(), now()->endOfWeek()]))
                    ->label('Esta semana'),
                TrashedFilter::make()->label('Eliminados')->hidden(),
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
