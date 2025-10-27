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
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon;

class TurnosTvsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->emptyStateHeading('No hay turnos de TV')
            ->emptyStateDescription('Creá un turno para reservar una TV portátil.')
            ->striped()
            ->paginated([25, 50, 100])
            ->columns([
                TextColumn::make('user.nombre_completo')
                    ->label('Profesor')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-m-user-circle'),

                TextColumn::make('inventario.nombre_equipo')
                    ->label('TV')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-m-tv'),

                TextColumn::make('fecha_turno')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-m-calendar-days')
                    ->tooltip(fn($record) => $record->fecha_turno?->format('d/m/Y')),

                TextColumn::make('hora_inicio')
                    ->label('Inicio')
                    ->time('H:i')
                    ->sortable()
                    ->icon('heroicon-m-clock'),

                TextColumn::make('hora_fin')
                    ->label('Fin')
                    ->time('H:i')
                    ->sortable()
                    ->icon('heroicon-m-clock'),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->icon(fn(string $state) => match ($state) {
                        'activo'     => 'heroicon-m-arrow-right-circle',
                        'confirmado' => 'heroicon-m-check-badge',
                        'finalizado' => 'heroicon-m-check-circle',
                        'cancelado'  => 'heroicon-m-x-circle',
                        default      => 'heroicon-m-information-circle',
                    })
                    ->colors([
                        'primary' => 'activo',
                        'success' => 'confirmado',
                        'gray'    => 'finalizado',
                        'danger'  => 'cancelado',
                    ]),
            ])
            ->filters([
                Filter::make('hoy')
                    ->label('Hoy')
                    ->query(fn(Builder $q) => $q->whereDate('fecha_turno', today()))
                    ->toggle()
                    ->indicateUsing(fn() => ['Hoy']),

                Filter::make('semana')
                    ->label('Esta semana')
                    ->query(fn(Builder $q) => $q->whereBetween('fecha_turno', [now()->startOfWeek(), now()->endOfWeek()]))
                    ->toggle()
                    ->indicateUsing(fn() => ['Semana actual']),

                // Por profesor
                SelectFilter::make('user_id')
                    ->label('Profesor')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                // Por estado
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'activo'     => 'Activo',
                        'confirmado' => 'Confirmado',
                        'cancelado'  => 'Cancelado',
                        'finalizado' => 'Finalizado',
                    ])
                    ->multiple()
                    ->preload(),

                // Rango de fechas (schema en v4)
                Filter::make('rango_fechas')
                    ->label('Rango de fechas')
                    ->schema([
                        DatePicker::make('desde')->label('Desde'),
                        DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['desde'] ?? null, fn(Builder $q, $date) => $q->whereDate('fecha_turno', '>=', $date))
                            ->when($data['hasta'] ?? null, fn(Builder $q, $date) => $q->whereDate('fecha_turno', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $chips = [];
                        if (! empty($data['desde'])) {
                            $chips[] = 'Desde ' . Carbon::parse($data['desde'])->format('d/m/Y');
                        }
                        if (! empty($data['hasta'])) {
                            $chips[] = 'Hasta ' . Carbon::parse($data['hasta'])->format('d/m/Y');
                        }
                        return $chips;
                    }),

                TrashedFilter::make()->label('Eliminados')->hidden(),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-m-pencil-square'),
                DeleteAction::make()
                    ->label('Eliminar')
                    ->icon('heroicon-m-trash'),
            ])
            ->toolbarActions([
                \Filament\Actions\CreateAction::make()
                    ->label('Nuevo turno')
                    ->icon('heroicon-m-plus')
                    ->color('primary'),
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar selección')
                        ->icon('heroicon-m-trash'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTurnosTvs::route('/'),
            'create' => CreateTurnosTv::route('/create'),
            'edit'   => EditTurnosTv::route('/{record}/edit'),
        ];
    }
}
