<?php

namespace App\Filament\Resources\InventarioBibliotecas\Tables;

use App\Models\InventarioBiblioteca;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InventarioBibliotecasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('portada_path')
                    ->label('Portada')
                    ->circular()
                    ->sortable()
                    ->size(44)
                    ->disk(config('filesystems.default', 'public')),

                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->weight('semibold'),

                TextColumn::make('autor')
                    ->label('Autor')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user')
                    ->toggleable(),

                TextColumn::make('isbn')
                    ->label('ISBN')
                    ->copyable()
                    ->sortable()
                    ->copyMessage('ISBN copiado')
                    ->toggleable(),

                TextColumn::make('estante')
                    ->label('Estante')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('columna')
                    ->label('Columna')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('categoria')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->icon('heroicon-m-tag')
                    ->toggleable(),

                TextColumn::make('cantidad')
                    ->label('Stock')
                    ->alignCenter()
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => $state > 2 ? 'success' : ($state > 0 ? 'warning' : 'danger')),

                IconColumn::make('disponible')
                    ->label('Disponible')
                    ->boolean()
                    ->sortable()
                    ->trueIcon('heroicon-m-check-circle')
                    ->falseIcon('heroicon-m-x-circle')
                    ->getStateUsing(fn(InventarioBiblioteca $record) => $record->disponible),

                TextColumn::make('created_at')
                    ->date('d/m/Y')
                    ->label('Alta')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('solo_disponibles')
                    ->label('Disponibles')
                    ->query(fn($q) => $q->disponibles())
                    ->toggle(),

                SelectFilter::make('categoria')
                    ->label('Categoría')
                    ->options(fn() => InventarioBiblioteca::query()
                        ->select('categoria')->whereNotNull('categoria')->distinct()->pluck('categoria', 'categoria')->toArray())
                    ->multiple()
                    ->indicator('Categoría'),
                SelectFilter::make('estante')
                    ->label('Estante')
                    ->options(fn() => InventarioBiblioteca::query()
                        ->select('estante')->whereNotNull('estante')->distinct()->pluck('estante', 'estante')->toArray())
                    ->indicator('Estante'),

                SelectFilter::make('columna')
                    ->label('Columna')
                    ->options(fn() => InventarioBiblioteca::query()
                        ->select('columna')->whereNotNull('columna')->distinct()->pluck('columna', 'columna')->toArray())
                    ->indicator('Columna'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Eliminar libro')
                    ->modalSubheading('Esta acción es irreversible.'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Eliminar seleccionados'),
                ]),
            ])
            ->striped();
    }
}
