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
use Filament\Forms\Components\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class InventariosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre_equipo')
                    ->label('Nombre del Equipo')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('categoria')
                    ->label('Categoría')
                    ->colors([
                        'primary' => 'notebook',
                        'warning' => 'tv_portatil',
                        'success' => 'proyector',
                        'info'    => 'impresora',
                        'gray'    => 'pc_escritorio',
                    ])
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'notebook' => 'Notebook',
                            'pc_escritorio' => 'PC de escritorio',
                            'tv_portatil' => 'TV portátil',
                            'proyector' => 'Proyector',
                            'impresora' => 'Impresora',
                            default => ucfirst((string)$state),
                        };
                    })
                    ->sortable(),
                TextColumn::make('marca')
                ->label('Marca')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('modelo')

                   ->label('Modelo')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('nro_serie')
                    ->label('N° de serie')
                    ->copyable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'success' => 'disponible',
                        'warning' => 'prestado',
                        'danger'  => 'en_reparacion',
                        'gray'    => 'baja',
                    ])
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'disponible' => 'Disponible',
                            'prestado' => 'Prestado',
                            'en_reparacion' => 'En reparación',
                            'baja' => 'Baja',
                            default => ucfirst((string)$state),
                        };
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('categoria')
                    ->label('Categoría')
                    ->options([
                        'notebook'     => 'Notebook',
                        'pc_escritorio'=> 'PC de escritorio',
                        'tv_portatil'  => 'TV portátil',
                        'proyector'    => 'Proyector',
                        'impresora'    => 'Impresora',
                        'otro'         => 'Otro',
                    ]),
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'disponible'    => 'Disponible',
                        'prestado'      => 'Prestado',
                        'en_reparacion' => 'En reparación',
                        'baja'          => 'Baja',
                    ])
                    ->multiple(),
                TernaryFilter::make('con_observaciones')
                    ->label('Con observaciones')
                    ->nullable()
                    ->queries(
                        true: fn (Builder $q) => $q->whereNotNull('observaciones')->where('observaciones', '!=', ''),
                        false: fn (Builder $q) => $q->whereNull('observaciones')->orWhere('observaciones', ''),
                    ),

            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
        // Podés añadir relation managers para Prestamos y TurnosTv más adelante
        return [
            // InventarioResource\RelationManagers\PrestamosRelationManager::class,
            // InventarioResource\RelationManagers\TurnosTvRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInventarios::route('/'),
            'create' => CreateInventario::route('/create'),
            'edit' => EditInventario::route('/{record}/edit'),
            //'view' => ViewInventario::route('/{record}'),
        ];
    }
}
