<?php

namespace App\Filament\Resources\PrestamoBibliotecas\Tables;

use App\Models\PrestamoBiblioteca;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PrestamoBibliotecasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('libro.titulo')->label('Libro')->searchable()->wrap(),
                TextColumn::make('libro.autor')->label('Autor')->searchable(),
                BadgeColumn::make('estado')
                    ->colors([
                        'warning' => 'vencido',
                        'success' => 'devuelto',
                        'danger'  => 'perdido',
                        'primary' => 'activo',
                    ]),
                TextColumn::make('fecha_prestamo')->date('d/m/Y')->label('Inicio'),
                TextColumn::make('fecha_vencimiento')->date('d/m/Y')->label('Vence'),
                TextColumn::make('fecha_devolucion')->date('d/m/Y')->label('Devuelto')->toggleable(),
                TextColumn::make('renovaciones')->label('Renov.')->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('estado')->options([
                    'activo' => 'Activo',
                    'vencido' => 'Vencido',
                    'devuelto' => 'Devuelto',
                    'perdido' => 'Perdido',
                ]),
                Filter::make('vence_hoy')
                    ->label('Vence hoy')
                    ->query(fn(Builder $q) => $q->whereDate('fecha_vencimiento', today())),
                Filter::make('activos')
                    ->query(fn(Builder $q) => $q->where('estado', 'activo')),
            ])
            ->recordActions([
                Action::make('devolver')
                    ->label('Devolver')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->requiresConfirmation()
                    ->visible(fn(PrestamoBiblioteca $r) => !in_array($r->estado, ['devuelto', 'perdido']))
                    ->action(fn(PrestamoBiblioteca $r) => $r->marcarDevuelto()),
                Action::make('renovar')
                    ->label('Renovar +7 días')
                    ->icon('heroicon-m-arrow-path')
                    ->visible(fn(PrestamoBiblioteca $r) => in_array($r->estado, ['activo', 'vencido']))
                    ->action(fn(PrestamoBiblioteca $r) => $r->renovar(7)),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
