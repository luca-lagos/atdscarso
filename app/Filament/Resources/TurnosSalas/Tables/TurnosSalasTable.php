<?php

namespace App\Filament\Resources\TurnosSalas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;


class TurnosSalasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->emptyStateHeading('No hay turnos de sala')
            ->emptyStateDescription('Creá un turno para reservar la sala de informática.')
            ->striped()
            ->paginated([25, 50, 100])
            ->columns([
                TextColumn::make('user.nombre_completo')
                    ->label('Profesor')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user-circle'),

                TextColumn::make('curso')
                    ->label('Curso')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('division')
                    ->label('División')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->searchable(),

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

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->colors([
                        'primary' => 'permanente',
                        'warning' => 'temporal',
                    ])
                    ->formatStateUsing(fn(string $state) => ucfirst($state))
                    ->toggleable(),

                // 🔹 NUEVO: Estado
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->colors([
                        'success' => 'activo',
                        'warning' => 'pendiente',
                        'danger'  => 'cancelado',
                    ])
                    ->formatStateUsing(fn(string $state) => ucfirst($state))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->since()
                    ->tooltip(fn($record) => $record->created_at?->format('d/m/Y H:i'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('hoy')
                    ->label('Hoy')
                    ->query(fn(Builder $q) => $q->whereDate('fecha_turno', today()))
                    ->toggle()
                    ->indicateUsing(fn(): array => ['Hoy']),

                Filter::make('semana')
                    ->label('Esta semana')
                    ->query(fn(Builder $q) => $q->whereBetween('fecha_turno', [now()->startOfWeek(), now()->endOfWeek()]))
                    ->toggle()
                    ->indicateUsing(fn(): array => ['Semana actual']),

                SelectFilter::make('user_id')
                    ->label('Profesor')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                // 🔹 NUEVO: filtro por estado
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'activo'    => 'Activo',
                        'pendiente' => 'Pendiente',
                        'cancelado' => 'Cancelado',
                    ])
                    ->multiple()
                    ->indicator('Estado'),

                // Rango de fechas
                Filter::make('rango_fechas')
                    ->label('Rango de fechas')
                    ->schema([
                        DatePicker::make('desde')->label('Desde'),
                        DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['desde']) && !empty($data['hasta'])) {
                            return $query->whereBetween('fecha_turno', [
                                Carbon::parse($data['desde'])->startOfDay(),
                                Carbon::parse($data['hasta'])->endOfDay(),
                            ]);
                        }

                        return $query
                            ->when(
                                $data['desde'] ?? null,
                                fn(Builder $q, $date) => $q->where('fecha_turno', '>=', Carbon::parse($date)->startOfDay())
                            )
                            ->when(
                                $data['hasta'] ?? null,
                                fn(Builder $q, $date) => $q->where('fecha_turno', '<=', Carbon::parse($date)->endOfDay())
                            );
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
                ViewAction::make()
                    ->label('Ver')
                    ->icon('heroicon-m-eye'),
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
}
