<?php

namespace App\Filament\Resources\InventarioBibliotecas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

class InventarioBibliotecasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('portada_path')->label('')->circular()->size(40),
                Tables\Columns\TextColumn::make('titulo')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('autor')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('isbn')->label('ISBN')->toggleable()->copyable(),
                Tables\Columns\TextColumn::make('editorial')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('categoria')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('disponible')
                    ->label('Disponible')
                    ->boolean()
                    ->getStateUsing(fn(InventarioBiblioteca $record) => $record->disponible),
                Tables\Columns\TextColumn::make('created_at')->date('d/m/Y')->label('Alta'),
            ])
            ->filters([
                Tables\Filters\Filter::make('solo_disponibles')
                    ->label('Disponibles')
                    ->query(fn($q) => $q->disponibles()),
                Tables\Filters\SelectFilter::make('categoria')->options(fn() => InventarioBiblioteca::query()
                    ->select('categoria')->whereNotNull('categoria')->distinct()->pluck('categoria', 'categoria')->toArray()),
            ])
            ->recordActions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
