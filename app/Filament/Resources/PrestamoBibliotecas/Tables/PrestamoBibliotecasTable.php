<?php

namespace App\Filament\Resources\PrestamoBibliotecas\Tables;

use App\Models\PrestamoBiblioteca;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

class PrestamoBibliotecasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('libro.titulo')->label('Libro')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('libro.autor')->label('Autor')->searchable(),
                Tables\Columns\BadgeColumn::make('estado')
                    ->colors([
                        'warning' => 'vencido',
                        'success' => 'devuelto',
                        'danger'  => 'perdido',
                        'primary' => 'activo',
                    ]),
                Tables\Columns\TextColumn::make('fecha_prestamo')->date('d/m/Y')->label('Inicio'),
                Tables\Columns\TextColumn::make('fecha_vencimiento')->date('d/m/Y')->label('Vence'),
                Tables\Columns\TextColumn::make('fecha_devolucion')->date('d/m/Y')->label('Devuelto')->toggleable(),
                Tables\Columns\TextColumn::make('renovaciones')->label('Renov.')->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')->options([
                    'activo' => 'Activo',
                    'vencido' => 'Vencido',
                    'devuelto' => 'Devuelto',
                    'perdido' => 'Perdido',
                ]),
                Tables\Filters\Filter::make('vence_hoy')
                    ->label('Vence hoy')
                    ->query(fn(Builder $q) => $q->whereDate('fecha_vencimiento', today())),
                Tables\Filters\Filter::make('activos')
                    ->query(fn(Builder $q) => $q->where('estado', 'activo')),
            ])
            ->recordActions([
                Tables\Actions\Action::make('devolver')
                    ->label('Devolver')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->requiresConfirmation()
                    ->visible(fn(PrestamoBiblioteca $r) => !in_array($r->estado, ['devuelto', 'perdido']))
                    ->action(fn(PrestamoBiblioteca $r) => $r->marcarDevuelto()),
                Tables\Actions\Action::make('renovar')
                    ->label('Renovar +7 días')
                    ->icon('heroicon-m-arrow-path')
                    ->visible(fn(PrestamoBiblioteca $r) => in_array($r->estado, ['activo', 'vencido']))
                    ->action(fn(PrestamoBiblioteca $r) => $r->renovar(7)),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
