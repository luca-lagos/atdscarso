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
                ImageColumn::make('portada_path')->label('')->circular()->size(40),
                TextColumn::make('titulo')->searchable()->wrap(),
                TextColumn::make('autor')->searchable()->toggleable(),
                TextColumn::make('isbn')->label('ISBN')->toggleable()->copyable(),
                TextColumn::make('editorial')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('categoria')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('cantidad')->label('Stock'),
                IconColumn::make('disponible')
                    ->label('Disponible')
                    ->boolean()
                    ->getStateUsing(fn(InventarioBiblioteca $record) => $record->disponible),
                TextColumn::make('created_at')->date('d/m/Y')->label('Alta'),
            ])
            ->filters([
                Filter::make('solo_disponibles')
                    ->label('Disponibles')
                    ->query(fn($q) => $q->disponibles()),
                SelectFilter::make('categoria')->options(fn() => InventarioBiblioteca::query()
                    ->select('categoria')->whereNotNull('categoria')->distinct()->pluck('categoria', 'categoria')->toArray()),
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
