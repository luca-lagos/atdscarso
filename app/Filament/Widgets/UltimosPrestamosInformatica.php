<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Prestamos\PrestamoResource;
use App\Models\Prestamo;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UltimosPrestamosInformaticaWidget extends BaseWidget
{
    protected static ?string $heading = 'Últimos préstamos (Informática)';
    protected static ?int $sort = 21;
    protected static string $color = 'success';

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super-admin')
            || (auth()->user()?->can('view_any_prestamos') ?? false);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Prestamo::query()
                    ->with(['inventario:id,nombre_equipo', 'user:id,name'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('inventario.nombre_equipo')->label('Equipo')->wrap()->limit(30)
                    ->sortable(),
                TextColumn::make('user.name')->label('Usuario')->limit(22)
                    ->sortable(),
                TextColumn::make('fecha_prestamo')->label('Inicio')->date('d/m')
                    ->sortable(),
                TextColumn::make('fecha_vencimiento')->label('Vence')->date('d/m'),
                TextColumn::make('estado')
                    ->badge()
                    ->sortable()
                    ->colors([
                        'warning' => ['pendiente', 'vencido'],
                        'success' => 'devuelto',
                        'danger'  => 'perdido',
                        'primary' => 'activo',
                    ])
                    ->label('Estado'),
            ])
            ->headerActions([
                Action::make('ver_todos')
                    ->label('Ver todos')
                    ->icon('heroicon-m-arrow-right')
                    ->url(PrestamoResource::getUrl('index'))
                    ->color('gray'),
            ])
            ->paginated(false)
            ->striped()
            ->emptyStateHeading('Sin préstamos recientes');
    }
}
