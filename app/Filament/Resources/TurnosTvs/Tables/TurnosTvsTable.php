<?php

namespace App\Filament\Resources\TurnosTvs\Tables;

use App\Filament\Resources\TurnosTvs\Pages\CreateTurnosTv;
use App\Filament\Resources\TurnosTvs\Pages\EditTurnosTv;
use App\Filament\Resources\TurnosTvs\Pages\ListTurnosTvs;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TurnosTvsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.nombre_completo')->label('Profesor')
                    ->sortable(),
                TextColumn::make('inventario.nombre_equipo')->label('TV')
                    ->sortable(),
                TextColumn::make('fecha_turno')
                    ->date('d/m/Y'),
                TextColumn::make('hora_inicio')
                    ->time('H:i'),
                TextColumn::make('hora_fin')
                    ->time('H:i'),
                TextColumn::make('estado')
                    ->badge()
                    ->colors([
                        'success' => 'activo',
                        'warning' => 'confirmado',
                        'gray'    => 'finalizado',
                        'danger'  => 'cancelado',
                    ]),
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
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTurnosTvs::route('/'),
            'create' => CreateTurnosTv::route('/create'),
            'edit' => EditTurnosTv::route('/{record}/edit'),
        ];
    }
}
