<?php

namespace App\Filament\Resources\Prestamos\Tables;

use App\Filament\Resources\Prestamos\Pages\CreatePrestamo;
use App\Filament\Resources\Prestamos\Pages\EditPrestamo;
use App\Filament\Resources\Prestamos\Pages\ListPrestamos;
use App\Models\Prestamo;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class PrestamosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->emptyStateHeading('No hay préstamos registrados')
            ->emptyStateDescription('Registrá el primer préstamo para comenzar a gestionar los equipos.')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Registrar préstamo')
                    ->icon('heroicon-m-plus')
                    ->url(CreatePrestamo::getUrl()),
            ])
            ->striped()
            ->paginated([25, 50, 100])
            ->searchPlaceholder('Buscar por equipo o profesor...')
            ->columns([
                TextColumn::make('inventario.nombre_equipo')
                    ->label('Equipo')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-m-computer-desktop')
                    ->iconColor('primary')
                    ->wrap(),

                TextColumn::make('user.name')
                    ->label('Profesor')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-m-user-circle'),

                TextColumn::make('fecha_prestamo')
                    ->label('Préstamo')
                    ->date('d/m/Y')
                    ->sortable()
                    ->tooltip(fn($record) => $record->fecha_prestamo?->format('d/m/Y')),

                TextColumn::make('fecha_devolucion')
                    ->label('Devolución prevista')
                    ->date('d/m/Y')
                    ->sortable()
                    ->tooltip(fn($record) => $record->fecha_devolucion?->format('d/m/Y')),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->icon(fn(string $state) => match ($state) {
                        'activo'   => 'heroicon-m-arrow-right-circle',
                        'vencido'  => 'heroicon-m-exclamation-triangle',
                        'cerrado'  => 'heroicon-m-check-circle',
                        default    => 'heroicon-m-information-circle',
                    })
                    ->colors([
                        'success' => 'cerrado',
                        'warning' => 'vencido',
                        'primary' => 'activo',
                    ])
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'activo'  => 'Activo',
                        'vencido' => 'Vencido',
                        'cerrado' => 'Cerrado',
                        default   => ucfirst($state),
                    }),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->since()
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
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'activo'  => 'Activo',
                        'vencido' => 'Vencido',
                        'cerrado' => 'Cerrado',
                    ])
                    ->multiple()
                    ->preload()
                    ->default(['activo']),

                // Filtro por profesor
                SelectFilter::make('user_id')
                    ->label('Profesor')
                    ->options(
                        User::query()
                            ->where('rol', 'profesor')
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    /*->relationship('user', 'name', modifyQueryUsing: fn(Builder $q) => $q->where('rol', 'profesor'))*/
                    ->searchable()
                    ->preload(),

                // Rango de fechas de préstamo
                Filter::make('rango_prestamo')
                    ->schema([
                        DatePicker::make('desde')->label('Desde')->native(false),
                        DatePicker::make('hasta')->label('Hasta')->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['desde'] ?? null, fn($q, $date) => $q->whereDate('fecha_prestamo', '>=', $date))
                            ->when($data['hasta'] ?? null, fn($q, $date) => $q->whereDate('fecha_prestamo', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if (! empty($data['desde'])) {
                            $indicators[] = 'Desde ' . \Carbon\Carbon::parse($data['desde'])->format('d/m/Y');
                        }
                        if (! empty($data['hasta'])) {
                            $indicators[] = 'Hasta ' . \Carbon\Carbon::parse($data['hasta'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make()
                    ->label('Editar')
                    ->tooltip('Editar préstamo')
                    ->icon('heroicon-m-pencil-square'),

                DeleteAction::make()
                    ->label('Eliminar')
                    ->tooltip('Eliminar préstamo')
                    ->icon('heroicon-m-trash'),

                Action::make('descargar_pdf')
                    ->label('Comodato')
                    ->tooltip('Descargar comprobante PDF')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->url(fn(Prestamo $record) => $record->pdf_url)
                    ->openUrlInNewTab()
                    ->visible(fn(Prestamo $record) => $record->pdf_path && Storage::exists($record->pdf_path)),
            ])
            ->toolbarActions([
                CreateAction::make()
                    ->label('Registrar préstamo')
                    ->icon('heroicon-m-plus')
                    ->color('primary'),
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar selección')
                        ->icon('heroicon-m-trash'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPrestamos::route('/'),
            'create' => CreatePrestamo::route('/create'),
            'edit'   => EditPrestamo::route('/{record}/edit'),
        ];
    }
}
