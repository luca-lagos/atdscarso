<?php

namespace App\Filament\Resources\Prestamos\Tables;

use App\Filament\Resources\Prestamos\Pages\CreatePrestamo;
use App\Filament\Resources\Prestamos\Pages\EditPrestamo;
use App\Filament\Resources\Prestamos\Pages\ListPrestamos;
use App\Models\Prestamo;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class PrestamosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('inventario.nombre_equipo')
                    ->label('Equipo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.nombre_completo')
                    ->label('Profesor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha_prestamo')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('fecha_devolucion')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('estado')
                    ->badge()
                    ->colors([
                        'success' => 'activo',
                        'warning' => 'vencido',
                        'gray'    => 'cerrado',
                    ]),
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
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),

                Action::make('descargar_pdf')
                ->label('Descargar Comodato')
                ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Prestamo $record) => $record->pdf_url)
                    ->openUrlInNewTab()
                    ->visible(fn (Prestamo $record) => $record->pdf_path && Storage::exists($record->pdf_path)),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrestamos::route('/'),
            'create' => CreatePrestamo::route('/create'),
            'edit' => EditPrestamo::route('/{record}/edit'),
        ];
    }
}
