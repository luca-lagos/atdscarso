<?php

namespace App\Filament\Resources\Inventarios\Tables;

use App\Filament\Resources\Inventarios\Pages\CreateInventario;
use App\Filament\Resources\Inventarios\Pages\EditInventario;
use App\Filament\Resources\Inventarios\Pages\ListInventarios;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InventariosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Aún no hay equipos cargados')
            ->emptyStateDescription('Comenzá creando el primer equipo para gestionar el inventario.')
            ->emptyStateActions([
                \Filament\Actions\Action::make('create')
                    ->label('Cargar equipo')
                    ->icon('heroicon-m-plus')
                    ->url(CreateInventario::getUrl()),
            ])
            ->striped() // mejora legibilidad en filas
            ->paginated([25, 50, 100])
            ->searchPlaceholder('Buscar por nombre, serie, marca o modelo...')
            ->columns([
                TextColumn::make('nombre_equipo')
                    ->label('Equipo')
                    ->sortable()
                    ->searchable()
                    ->wrap()
                    ->description(fn($record) => $record->modelo ?: null, position: 'below')
                    ->icon('heroicon-m-computer-desktop')
                    ->iconColor('primary'),

                TextColumn::make('categoria')
                    ->label('Categoría')
                    ->badge()
                    ->colors([
                        'primary' => 'notebook',
                        'warning' => 'tv_portatil',
                        'success' => 'proyector',
                        'info'    => 'impresora',
                        'gray'    => 'pc_escritorio',
                    ])
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'notebook'      => 'Notebook',
                            'pc_escritorio' => 'PC de escritorio',
                            'tv_portatil'   => 'TV portátil',
                            'proyector'     => 'Proyector',
                            'impresora'     => 'Impresora',
                            default         => ucfirst((string) $state),
                        };
                    })
                    ->sortable(),

                TextColumn::make('marca')
                    ->label('Marca')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('nro_serie')
                    ->label('N° de serie')
                    ->copyable()
                    ->copyMessage('Serie copiada')
                    ->copyMessageDuration(1500)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->icon(fn(string $state) => match ($state) {
                        'disponible'    => 'heroicon-m-check-circle',
                        'prestado'      => 'heroicon-m-arrow-right-circle',
                        'en_reparacion' => 'heroicon-m-wrench-screwdriver',
                        'baja'          => 'heroicon-m-x-circle',
                        default         => 'heroicon-m-information-circle',
                    })
                    ->colors([
                        'success' => 'disponible',
                        'warning' => 'prestado',
                        'danger'  => 'en_reparacion',
                        'gray'    => 'baja',
                    ])
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'disponible'    => 'Disponible',
                            'prestado'      => 'Prestado',
                            'en_reparacion' => 'En reparación',
                            'baja'          => 'Baja',
                            default         => ucfirst((string) $state),
                        };
                    })
                    ->sortable(),

                TextColumn::make('cantidad')
                    ->label('Stock')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->since() // “hace 3 días”
                    ->tooltip(fn($record) => $record->updated_at?->format('d/m/Y H:i'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('categoria')
                    ->label('Categoría')
                    ->options([
                        'notebook'      => 'Notebook',
                        'pc_escritorio' => 'PC de escritorio',
                        'tv_portatil'   => 'TV portátil',
                        'proyector'     => 'Proyector',
                        'impresora'     => 'Impresora',
                        'otro'          => 'Otro',
                    ])
                    ->native(false)
                    ->preload(),

                SelectFilter::make('estado')
                    ->label('Estado')
                    ->multiple()
                    ->options([
                        'disponible'    => 'Disponible',
                        'prestado'      => 'Prestado',
                        'en_reparacion' => 'En reparación',
                        'baja'          => 'Baja',
                    ])
                    ->native(false)
                    ->preload()
                    ->default(['disponible']),

                TernaryFilter::make('con_observaciones')
                    ->label('Con observaciones')
                    ->nullable()
                    ->native(false)
                    ->queries(
                        true: fn(Builder $q) => $q->whereNotNull('observaciones')->where('observaciones', '!=', ''),
                        false: fn(Builder $q) => $q->whereNull('observaciones')->orWhere('observaciones', ''),
                    ),

                // Preset de “Sólo disponibles”
                Filter::make('solo_disponibles')
                    ->label('Sólo disponibles')
                    ->query(fn(Builder $q) => $q->where('estado', 'disponible'))
                    ->toggle(),

            ], layout: FiltersLayout::AboveContent) // filtros arriba tipo “toolbar”
            ->recordUrl(fn($record) => EditInventario::getUrl([$record]))
            ->recordActions([
                ViewAction::make()
                    ->label('Ver')
                    ->tooltip('Ver detalles')
                    ->icon('heroicon-m-eye'),
                EditAction::make()
                    ->label('Editar')
                    ->tooltip('Editar equipo')
                    ->icon('heroicon-m-pencil-square'),
                DeleteAction::make()
                    ->label('Eliminar')
                    ->tooltip('Eliminar equipo')
                    ->icon('heroicon-m-trash'),
            ])
            ->toolbarActions([
                \Filament\Actions\CreateAction::make()
                    ->label('Nuevo equipo')
                    ->icon('heroicon-m-plus')
                    ->color('primary'),
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar selección')
                        ->icon('heroicon-m-trash'),
                ]),
            ])
            ->defaultSort('nombre_equipo');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre_equipo', 'nro_serie', 'marca', 'modelo'];
    }

    public static function getRelations(): array
    {
        return [
            // Relation managers futuros: préstamos, turnos, etc.
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListInventarios::route('/'),
            'create' => CreateInventario::route('/create'),
            'edit'   => EditInventario::route('/{record}/edit'),
            // 'view' => ViewInventario::route('/{record}'),
        ];
    }
}
