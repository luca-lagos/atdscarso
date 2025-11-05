<?php

namespace App\Filament\Resources\PrestamoBibliotecas\Tables;

use App\Models\PrestamoBiblioteca;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
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
                TextColumn::make('libro.titulo')->label('Libro')->searchable()->sortable()->wrap(),
                TextColumn::make('libro.autor')->label('Autor')->searchable()->sortable(),
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-m-user-circle'),
                TextColumn::make('estado')
                    ->badge()->sortable()
                    ->label('Estado')
                    ->colors([
                        'warning' => 'vencido',
                        'success' => 'devuelto',
                        'danger'  => 'perdido',
                        'primary' => 'activo',
                    ]),
                TextColumn::make('fecha_prestamo')->sortable()->date('d/m/Y')->label('Inicio'),
                TextColumn::make('fecha_vencimiento')->sortable()->date('d/m/Y')->label('Vence'),
                TextColumn::make('fecha_devolucion')->date('d/m/Y')->label('Devuelto')->sortable()->toggleable(),
                TextColumn::make('renovaciones')->label('Renov.')->alignCenter()->sortable(),
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
                Filter::make('pendientes')->query(fn($q) => $q->where('estado', 'pendiente'))
            ])
            ->recordActions([
                Action::make('confirmar')
                    ->label('Confirmar')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn($r) => $r->estado === 'pendiente' && auth()->user()->can('update_prestamo_biblioteca'))
                    ->action(function (PrestamoBiblioteca $r) {
                        $r->update(['estado' => 'activo']);
                        Notification::make()
                            ->title('Préstamo confirmado')
                            ->success()
                            ->send();
                    }),
                Action::make('devolver')
                    ->label('Devolver')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->requiresConfirmation()
                    ->visible(fn(PrestamoBiblioteca $r) => !in_array($r->estado, ['devuelto', 'perdido']))
                    ->action(function (PrestamoBiblioteca $r) {
                        $r->marcarDevuelto();
                        Notification::make()
                            ->title('Devolución registrada')
                            ->success()
                            ->send();
                    }),
                Action::make('renovar')
                    ->label('Renovar +7 días')
                    ->icon('heroicon-m-arrow-path')
                    ->visible(fn(PrestamoBiblioteca $r) => in_array($r->estado, ['activo', 'vencido']))
                    ->action(function (PrestamoBiblioteca $r) {
                        $r->renovar(7);
                        Notification::make()
                            ->title('Préstamo renovado por 7 días')
                            ->success()
                            ->send();
                    }),
                EditAction::make()->requiresConfirmation()->after(function () {
                    Notification::make()
                        ->title('Préstamo actualizado')
                        ->success()
                        ->send();
                }),
                DeleteAction::make()->requiresConfirmation()->after(function () {
                    Notification::make()
                        ->title('Préstamo eliminado')
                        ->success()
                        ->send();
                }),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()->requiresConfirmation()->after(function () {
                    Notification::make()
                        ->title('Préstamos eliminados')
                        ->success()
                        ->send();
                }),
            ]);
    }
}
